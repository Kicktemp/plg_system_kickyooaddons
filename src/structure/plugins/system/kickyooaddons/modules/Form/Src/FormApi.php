<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Form\Src;

use DateTime;
use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Event\DispatcherInterface;
use RuntimeException;
use YOOtheme\Encrypter;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\Translator;
use YOOtheme\Url;

class FormApi
{
	/**
	 * The settings
	 *
	 * @var    array
	 * @since  3.4
	 */
	public $parseSettings = [
		'subject',
		'from',
		'fromname',
		'body',
		'recipients',
		'reply_tos',
		'subject_copy',
		'body_copy',
		'from_copy',
		'fromname_copy',
		'recipients_copy',
		'message'
	];

	/**
	 * @var string
	 */
	protected $secret;

	public function __construct(string $secret)
	{
		$this->secret = $secret;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit(Request $request, Response $response, Translator $translator)
	{
		$hash = $request->getQueryParam('hash');
		$settings = $request->getParam('settings');

		$request->abortIf($hash !== $this->getHash($settings), 400, 'Invalid settings hash');

		try {
			$settings = $this->decodeData($settings);
		} catch (\Exception $e) {
			return $response->withJson($e->getMessage(), 400);
		}

		$data = $request->getParsedBody();

		$files = $request->getUploadedFiles();

        // Process the content plugins.
        PluginHelper::importPlugin('content');
        Factory::getApplication()->triggerEvent('onKickyooaddonsBeforeSubmit', ['plg_system_kickyooaddons.formsubmit', $data, $settings, $files]);


        foreach ($this->parseSettings as $settingField) {
			if (!($settings[$settingField] ?? false)) {
				continue;
			}

			$settings[$settingField] = $this->_parseTags($settings[$settingField], $data);
		}

        if (isset($settings['joomlasession']) and $settings['joomlasession']) {
            $valid = Session::checkToken('post');

            if (!$valid) {
                return $response->withJson(Text::_('JINVALID_TOKEN_NOTICE'), 403);
            }
        }

		if (isset($settings['kick_honeypot']) and $settings['kick_honeypot'])
		{
			$time = $settings['kick_honeypot'];
			$time = DateTime::createFromFormat('U', $time);

			if (!$time) {
				return $response->withJson(Text::_('PLG_SYSTEM_KICKYOOFORM_HONEYPOT_NO_TIME'), 403);
			}

			$now = new DateTime();
			if ($time >= $now) {
				return $response->withJson(Text::_('PLG_SYSTEM_KICKYOOFORM_HONEYPOT_TIME_BIGGER_NOW'), 403);
			}

			$seconds = ($now->getTimestamp() - $time->getTimestamp());
			$minSeconds = $settings['min_seconds'] ?? 1;
			if ($seconds < $minSeconds) {
                $return = [
                    'success' => false,
                    'wait' => $minSeconds - $seconds,
                    'statusText' => Text::_($settings['kick_honeypoterror'])
                ];
                return $response->withJson($return, 403);
			}

			if (isset($settings['kick_honeypotfield']) && isset($data[$settings['kick_honeypotfield']]) && $data[$settings['kick_honeypotfield']] !== "") {
				return $response->withJson(Text::_($settings['kick_honeypotmessage']), 403);
			}
		}

		if (isset($settings['captcha']) and $settings['captcha'])
		{
			try
			{
				$app = Factory::getApplication();
				$default = $app->get('captcha');
				$captcha = Captcha::getInstance($default, array('namespace' => 'kickform'));

                if ($default === 'recaptcha') {
                    $captcha->checkAnswer($data['g-recaptcha-response']);
                } elseif ($default === 'easycalccheckcaptcha') {
                    if (!$captcha->checkAnswer($data[$settings['captcha']])) {
                        $message = Factory::getApplication()->getMessageQueue()[0]['message'];
                        return $response->withJson($message, 403);
                    }
                }
			}
			catch (\RuntimeException $e)
			{
				return $response->withJson($e->getMessage(), 400);
			}
		}

		// Check Attachment
		if (count($files))
		{
			$settings['files'] = $files;
		}

		try {
			$return = $this->sendMail($settings);
		} catch (\Exception $e) {
			return $response->withJson($e->getMessage(), 400);
		}

		try {
			if($settings['email_copy'])
			{
				$this->sendMailCopy($settings);
			}
		} catch (\Exception $e) {
			return $response->withJson($e->getMessage(), 400);
		}

        Factory::getApplication()->triggerEvent('onKickyooaddonsAfterSubmit', ['plg_system_kickyooaddons.formsubmit', $data, $settings, $files]);

        if (isset( $settings['kick_honeypot'])) {
            $settings['kick_honeypot'] = (new DateTime())->format('U');
            $return['settings'] = $this->encodeData($settings);
            $return['actionurl'] = Url::route('theme/kickform/submit', ['hash' => $this->getHash($return['settings'])]);
        }

		if ($settings['after_submit'] === 'redirect') {
			$return['redirect'] = Route::_($settings['redirect']);
		}
		elseif ($settings['after_submit'] === 'notification') {
            $return['notification']['pos'] = $settings['notification']['pos'];
            $return['notification']['timeout'] = $settings['notification']['timeout'];
            $return['notification']['status'] = $settings['notification']['status'];
            $return['notification']['message'] = $translator->trans($settings['message']);
		} else {
			$return['message'] = $translator->trans($settings['message']);
		}

		return $response->withJson($return);
	}

	public function sendMail($settings)
	{
		$mailer = Factory::getMailer();

		$recipients = explode(',', $settings['recipients'] ?? '');

		foreach ($recipients as $recipient) {
			if (!empty($recipient) && MailHelper::isEmailAddress($recipient))
			{
				$mailer->addRecipient($recipient);
			}
		}

		$ccs = explode(',', $settings['ccs'] ?? '');
		foreach ($ccs as $cc) {

			if (!empty($cc) && MailHelper::isEmailAddress($cc))
			{
				$mailer->addCc($cc);
			}
		}

		$bccs = explode(',', $settings['bccs'] ?? '');
		foreach ($bccs as $bcc) {
			if (!empty($bcc) && MailHelper::isEmailAddress($bcc))
			{
				$mailer->addBcc($bcc);
			}
		}

		$replyTos = explode(',', $settings['reply_tos'] ?? '');
		foreach ($replyTos as $replyTo) {
			if (!empty($replyTo) && MailHelper::isEmailAddress($replyTo))
			{
				$mailer->addReplyTo($replyTo);
			}

		}

		if ($from = $settings['from'] ?? false && $fromname = $settings['fromname'] ?? false) {
			$mailer->setFrom($from, $fromname);
		}

		if ($settings['subject'] ?? false) {
			$mailer->setSubject($settings['subject']);
		}

		if ($settings['body'] ?? false) {

			if ($settings['html'] ?? true) {
				$settings['body'] = nl2br($settings['body']);
			}

			$mailer->setBody($settings['body']);
		}

		if ($settings['html'] ?? true) {
			$mailer->isHtml(true);
		}

		// Attachment
		if (isset($settings['files']) && $settings['files'] ?? false) {

			foreach ($settings['files'] as $file)
			{
				$stream = $file->getStream()->getMetadata('uri');

				$mailer->addAttachment($stream, $file->getClientFilename(), $encoding = 'base64', $file->getClientMediaType());
			}
		}


		$mailResult = $mailer->Send();

		return [
			'success' => $mailResult,
			'data' => $mailResult ? 'send' :  $mailer->ErrorInfo
		];
	}

	public function sendMailCopy($settings)
	{
		$mailer = Factory::getMailer();

		$recipients = explode(',', $settings['recipients_copy'] ?? '');
		foreach ($recipients as $recipient) {
			if (!empty($recipient) && MailHelper::isEmailAddress($recipient))
			{
				$mailer->addRecipient($recipient);
			}
		}

		if ($from = $settings['from_copy'] ?? false && $fromname = $settings['fromname_copy'] ?? false) {
			$mailer->setFrom($from, $fromname);
		}

		if ($settings['subject_copy'] ?? false) {
			$mailer->setSubject($settings['subject_copy']);
		}

		if ($settings['body_copy'] ?? false) {

			if ($settings['html'] ?? true) {
				$settings['body_copy'] = nl2br($settings['body_copy']);
			}

			$mailer->setBody($settings['body_copy']);
		}

		if ($settings['html'] ?? true) {
			$mailer->isHtml(true);
		}

		$mailResult = $mailer->Send();

		return [
			'success' => $mailResult,
			'data' => $mailResult ? 'send' :  $mailer->ErrorInfo
		];
	}

	protected function _parseTags(string $content, $data): string
	{
		foreach ($data as $dataField => $value) {
			$tag = '{' . $dataField . '}';

			if (is_array($value)) {
				$value = implode(', ', $value);
			}

			$content = str_replace($tag, $value, $content);
		}

		return $content;
	}

	public function encodeData(array $data): string
	{
		return base64_encode(json_encode($data));
	}

	public function decodeData(string $data): array
	{
		return json_decode(base64_decode($data), true);
	}

	public function getHash(string $data): string
	{
		return hash('fnv132', hash_hmac('sha1', $data, $this->secret));
	}
}
