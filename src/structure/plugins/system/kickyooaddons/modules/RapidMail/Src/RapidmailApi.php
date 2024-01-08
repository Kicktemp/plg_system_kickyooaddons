<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\RapidMail\Src;

use YOOtheme\Encrypter;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\HttpClientInterface;
use YOOtheme\Str;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Translator;

/**
 *
 */
class RapidmailApi
{
	/**
	 * @var Translator
	 */
	protected $translator;
	/**
	 * @var
	 */
	protected $apiKey;
	/**
	 * @var string
	 */
	protected $apiUser;
	/**
	 * @var string
	 */
	protected $apiPassword;
	/**
	 * @var string
	 */
	protected $apiEndpoint = '';
	/**
	 * @var HttpClientInterface
	 */
	protected $client;
	/**
	 * @var string
	 */
	protected $secret;

	/**
	 * @param string              $apiUser
	 * @param string              $apiPassword
	 * @param HttpClientInterface $client
	 * @param Translator          $translator
	 *
	 * @throws \Exception
	 */
	public function __construct($apiUser, $apiPassword, string $secret, HttpClientInterface $client, Translator $translator)
	{
		$this->apiUser = $apiUser;
		$this->apiPassword = $apiPassword;
		$this->client = $client;
		$this->translator = $translator;
		$this->apiEndpoint = "https://apiv3.emailsys.net";

		$this->secret = $secret;
	}

	/**
	 * {@inheritdoc}
	 */
	public function recipientlists(Request $request, Response $response)
	{
		try {
			if ($result = $this->get("recipientlists", []) and $result['success']) {
				$lists = array_map(function ($list) {
					return ['value' => $list['id'], 'text' => $list['name'], 'description' => $list['description'], 'default' => $list['default']];
				}, $result['data']['_embedded']['recipientlists']);
			} else {
				throw new \Exception($result['data']);
			}

			return $response->withJson(compact('lists'));

		} catch (\Exception $e) {

			return $response->withJson($e->getMessage(), 400);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function subscribe(Request $request, Response $response, Translator $translator)
	{
		$hash = $request->getQueryParam('hash');
		$settings = $request->getParam('settings');

		$request->abortIf($hash !== $this->getHash($settings), 400, 'Invalid settings hash');

		try {
			$settings = $this->decodeData($settings);
		} catch (\Exception $e) {
			return $response->withJson($e->getMessage(), 400);
		}

		$firstname = $request('firstname', '');
		$lastname = $request('lastname', '');
		$recipientlist_id = $settings['recipientlist_id'];

		$body['email'] = $request('email');
		$body['recipientlist_id'] = $recipientlist_id;

		if (isset($firstname))
			$body['firstname'] = $firstname;

		if (isset($lastname))
			$body['lastname'] = $lastname;

		$url = 'recipients';

		if ($settings['optIn'])
			$url .= '?send_activationmail=yes';

		$return = $this->post($url, $body);

		if (!$return['success'])
		{
			return $response->withJson($return['data'], 400);
		}

		if ($settings['after_submit'] === 'redirect') {
			$return['redirect'] = $settings['redirect'];
		} else {
			$return['message'] = $translator->trans($settings['message']);
		}

		return $response->withJson($return);
	}

	/**
	 * @param string $name
	 * @param array  $args
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function get($name, $args = [])
	{
		return $this->request('GET', $name, $args);
	}

	/**
	 * @param string $name
	 * @param array  $args
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	public function post($name, $args = [])
	{
		return $this->request('POST', $name, $args);
	}

	/**
	 * @param string $method
	 * @param string $name
	 * @param array  $args
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	protected function request($method, $name, $args)
	{
		$url = "{$this->apiEndpoint}/{$name}";

		$headers = $this->getHeaders() + [
				'Accept' => 'application/json'
			];

		switch ($method) {
			case 'GET':

				$query = http_build_query($args, '', '&');
				$response = $this->client->get("{$url}?{$query}", compact('headers'));
				break;
			case 'POST':

				$response = $this->client->post($url, json_encode($args), compact('headers'));
				break;

			default:
				throw new \Exception("Call to undefined method {$method}");
		}

		$encoded = json_decode($response->getBody(), true);
		$success = $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299;

		return [
			'success' => $success,
			'data' => $success ? $encoded : $this->findError($response, $encoded),
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
				'Accept' => "application/json",
				'Content-Type' => "application/json",
				'Authorization' => "Basic " . base64_encode($this->apiUser . ':' . $this->apiPassword),
			];
	}

	/**
	 * @param array $data
	 * @return string
	 */
	public function encodeData(array $data): string
	{
		return base64_encode(json_encode($data));
	}

	/**
	 * @param string $data
	 * @return array
	 */
	public function decodeData(string $data): array
	{
		return json_decode(base64_decode($data), true);
	}

	/**
	 * @param string $data
	 * @return string
	 */
	public function getHash(string $data): string
	{
		return hash('fnv132', hash_hmac('sha1', $data, $this->secret));
	}
}
