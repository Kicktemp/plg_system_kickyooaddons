<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\HubSpot\Src;

use DateTime;
use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use YOOtheme\Encrypter;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\HttpClientInterface;
use YOOtheme\Translator;
use YOOtheme\Url;

class HubSpotApi
{
    /**
     * @var Translator
     */
    protected $translator;

    protected $apiKey;
    protected $portalId;

    protected $apiEndpoint = '';
    protected $apiFormsEndpoint = '';

    protected $guid = '';

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @param   string               $secret
     * @param   string               $apiKey
     * @param   string               $portalId
     * @param   HttpClientInterface  $client
     * @param   Translator           $translator
     *
     * @throws \Exception
     */
    public function __construct($secret, $apiKey, $portalId, HttpClientInterface $client, Translator $translator)
    {
        $this->secret     = $secret;
        $this->apiKey     = $apiKey;
        $this->portalId   = $portalId;
        $this->client     = $client;
        $this->translator = $translator;

        if (strpos($apiKey, '-') === false)
        {
            throw new \Exception('Invalid API key.');
        }

        $this->apiEndpoint = "https://api.hubapi.com/marketing/v3";
        $this->apiFormsEndpoint = "https://api.hsforms.com/submissions/v3";
    }

    public function fields(Request $request, Response $response)
    {
        $form = $request('form');
        try
        {

            if ($result = $this->get("forms/{$form}/", ['limit' => '50', 'offset' => '0', 'sort' => 'desc']) and $result['success'])
            {
                $fieldsmap = array();
                if (isset($result['data']['fieldGroups']))
                {
                    foreach ($result['data']['fieldGroups'] as $group)
                    {
                        if (isset($group['fields'] ))
                        {
                            foreach ($group['fields'] as $field)
                            {
                                $fieldsmap[] = $field;
                            }
                        }
                    }
                }
                $fields = array_map(function ($field) {
                    return ['value' => $field['name'], 'text' => $field['label'], 'required' => $field['required'], 'fieldType' => $field['fieldType']];
                }, $fieldsmap);
            }
            else
            {
                throw new \Exception($result['data']);
            }

            return $response->withJson(compact('fields'));

        }
        catch (\Exception $e)
        {

            return $response->withJson($e->getMessage(), 400);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function forms(Request $request, Response $response)
    {
        try
        {

            if ($result = $this->get('forms', ['limit' => '200', 'offset' => '0', 'sort' => 'desc']) and $result['success'])
            {
                $forms = $result['data']['results'];
            }
            else
            {
                throw new \Exception($result['data']);
            }

            return $response->withJson(compact('forms'));

        }
        catch (\Exception $e)
        {
            return $response->withJson($e->getMessage(), 400);
        }
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

        // Process the content plugins.
        PluginHelper::importPlugin('content');
        Factory::getApplication()->triggerEvent('onKickyooaddonsBeforeSubmit', ['plg_system_kickyooaddons.hubspotsubmit', $data, $settings]);


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
                $captcha = Captcha::getInstance($default, array('namespace' => 'kickhubspot'));

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

        $body = [];

        foreach ($settings['hubspot_fields'] as $field => $objectTypeId)
        {
            $value = $request($field, '');

            if (is_array($value))
            {
                $value = implode(',', $value);
            }
            $body['fields'][] = [
                "objectTypeId" => $objectTypeId,
                "name" => $field,
                "value" => $value
            ];
        }

        $subscriptions = $request('subscription', false);
        $subs = [];

        if ($subscriptions && count((array) $subscriptions))
        {
            foreach ($subscriptions as $subscription)
                $subs[$subscription] = true;
        }

        if (isset($settings['legalConsentOptions']['type']) && $settings['legalConsentOptions']['type'] === 'explicit_consent_to_process')
        {
            $body['legalConsentOptions'] = [
                'consent' => [
                    'consentToProcess' => (boolean) $request('consentToProcess', false),
                    'text' => $settings['legalConsentOptions']['consentToProcessCheckboxLabel'],
                ]
            ];

            if (isset($settings['legalConsentOptions']['communicationsCheckboxes'])
                && count($settings['legalConsentOptions']['communicationsCheckboxes']))
            {
                foreach ($settings['legalConsentOptions']['communicationsCheckboxes'] as $communication)
                {

                    $body['legalConsentOptions']['consent']['communications'][] = [
                        'value' => key_exists($communication['subscriptionTypeId'], $subs),
                        'subscriptionTypeId' => $communication['subscriptionTypeId'],
                        'text' => $communication['label'],
                    ];
                }
            }
        } elseif (isset($settings['legalConsentOptions']['type'])
            && $settings['legalConsentOptions']['type'] === 'implicit_consent_to_process'
            && isset($settings['legalConsentOptions']['communicationsCheckboxes'])
            && count($settings['legalConsentOptions']['communicationsCheckboxes'])
        ) {
            $communication = $settings['legalConsentOptions']['communicationsCheckboxes'][0];
            $body['legalConsentOptions'] = [
                'legitimateInterest' => [
                    'value' => key_exists($communication['subscriptionTypeId'], $subs),
                    'subscriptionTypeId' => $communication['subscriptionTypeId'],
                    'legalBasis' => 'CUSTOMER',
                    'text' => $communication['label']
                ]
            ];
        }

        $url = "integration/secure/submit/" . $this->portalId . '/' . $settings['guid'];
        $return = $this->post($url, $body);

        if ($settings['override'])
        {
            // TODO klären ob after_submit überschrieben werden soll
            if (isset($return['data']['inlineMessage']) && $return['data']['inlineMessage'] !== '') {
                $settings['message'] = $return['data']['inlineMessage'];
            } elseif (isset($return['data']['redirectUri']) && $return['data']['redirectUri'] !== '') {
                $settings['redirect'] = $return['data']['redirectUri'];
                $settings['after_submit'] = 'redirect';
            }
        }

        Factory::getApplication()->triggerEvent('onKickyooaddonsAfterSubmit', ['plg_system_kickyooaddons.hubspotsubmit', $data, $settings]);

        if (!$return['success'])
        {
            return $response->withJson('', 400);
        }

        if (isset( $settings['kick_honeypot'])) {
            $settings['kick_honeypot'] = (new DateTime())->format('U');
            $return['settings'] = $this->encodeData($settings);
            $return['actionurl'] = Url::route('theme/kickhubspot/hubsubmit', ['hash' => $this->getHash($return['settings'])]);
        }

        if ($settings['after_submit'] === 'redirect')
        {
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

    /**
     * @param   string  $name
     * @param   array   $args
     *
     * @return array
     * @throws \Exception
     *
     */
    public function get($name, $args = [])
    {
        return $this->request('GET', $name, $args);
    }

    /**
     * @param   string  $name
     * @param   array   $args
     *
     * @return array
     * @throws \Exception
     *
     */
    public function post($name, $args = [])
    {
        return $this->request('POST', $name, $args);
    }

    /**
     * @param   string  $method
     * @param   string  $name
     * @param   array   $args
     *
     * @return array
     * @throws \Exception
     *
     */
    protected function request($method, $name, $args)
    {
        $url = "{$this->apiEndpoint}/{$name}";

        $headers = $this->getHeaders() + [
                'Accept' => 'application/json'
            ];

        switch ($method)
        {
            case 'GET':
                $query    = http_build_query($args, '', '&');
                $response = $this->client->get("{$url}?{$query}", compact('headers'));
                break;
            case 'POST':
                $url = "{$this->apiFormsEndpoint}/{$name}";
                $body = json_encode($args);
                $response = $this->client->post("{$url}", $body, compact('headers'));
                break;

            default:
                throw new \Exception("Call to undefined method {$method}");
        }

        $encoded = json_decode($response->getBody(), true);
        $success = $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299;

        return [
            'success' => $success,
            'data'    => $success ? $encoded : $this->findError($response, $encoded),
        ];
    }

    /**
     * Get error message from response.
     *
     * @param $response
     * @param $formattedResponse
     *
     * @return string
     */
    protected function findError($response, $formattedResponse)
    {
        return isset($formattedResponse['detail'])
            ? $formattedResponse['detail']
            : 'Unknown error, call getLastResponse() to find out what happened.';
    }

    /**
     * {@inheritdoc}
     */
    protected function getHeaders()
    {
        return [
            'Accept'       => "application/json",
            'Content-Type' => "application/json",
            'Authorization'      => "Bearer {$this->apiKey}",
        ];
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