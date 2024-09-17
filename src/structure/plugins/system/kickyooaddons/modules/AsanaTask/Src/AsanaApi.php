<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\AsanaTask\Src;

use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\HttpClientInterface;
use YOOtheme\Str;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Translator;

class AsanaApi
{
	/**
	 * @var Translator
	 */
	protected $translator;

	protected $apiKey;

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
	 * @param string              $apiKey
	 * @param HttpClientInterface $client
	 * @param Translator          $translator
	 *
	 * @throws \Exception
	 */
	public function __construct(string $secret, $apiKey, HttpClientInterface $client, Translator $translator)
	{
		$this->secret = $secret;
		$this->apiKey = $apiKey;
		$this->client = $client;
		$this->translator = $translator;

		$this->apiEndpoint = "https://app.asana.com/api/1.0";
	}

	/**
	 * {@inheritdoc}
	 */
	public function projects(Request $request, Response $response)
	{
		$workspace  = $request('workspace');
		try {

			if ($result = $this->get("projects", ['workspace' => $workspace, 'archived' => 0]) and $result['success']) {
                $projects = array_map(function ($project) {
                    return ['value' => $project['gid'], 'text' => $project['name']];
                }, $result['data']['data']);
            } else {
                throw new \Exception($result['data']);
            }

            $key_values = array_column($projects, 'text');
            array_multisort($key_values, SORT_ASC, $projects);

            return $response->withJson(compact('projects'));

		} catch (\Exception $e) {

			return $response->withJson($e->getMessage(), 400);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function workspaces(Request $request, Response $response)
	{
		try {

			if ($result = $this->get('workspaces', []) and $result['success']) {
                $workspaces = array_map(function ($workspace) {
					return ['value' => $workspace['gid'], 'text' => $workspace['name']];
				}, $result['data']['data']);
			} else {
				throw new \Exception($result['data']);
			}

			return $response->withJson(compact('workspaces'));

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



		$mergeFields = [];
		$firstname = $request('first_name', '');
		$lastname = $request('last_name', '');
		$optIn = $request('privacy', '');
        $attributes = [];

        try {
            if ($result = $this->get('contacts/attributes', []) and $result['success']) {
                $attributes = $result['data']['attributes'];
            } else {
                throw new \Exception($result['data']);
            }
        } catch (\Exception $e) {
            return $response->withJson($e->getMessage(), 400);
        }

        foreach ($attributes as $attribute) {
            if (!isset($attribute['field_key'])) {
                continue;
            }

            if ($attribute['field_key'] === 'firstname' && $firstname !== '') {
                $mergeFields[$attribute['name']] = $firstname;
            }


            if ($attribute['field_key'] === 'lastname' && $lastname !== '') {
                $mergeFields[$attribute['name']] = $lastname;
            }
        }

		if (isset($optIn)) {
			$mergeFields['OPT_IN'] = (boolean) $optIn;
		}

		$listIds = $settings['includeListIds'];
		$redirectionUrl = \JRoute::_(Uri::root() . $settings['redirectionUrl']); //TODO

		if ($settings['optIn'])
		{
			$return = $this->post("contacts/doubleOptinConfirmation", [
				'email'          => $request('email'),
				'includeListIds' => $listIds,
				'templateId'     => (int) $settings['templateId'],
				'redirectionUrl' => $redirectionUrl,
				'attributes'     => $mergeFields,
			]);

			if (!$return['success'])
			{
				return $response->withJson('', 400);
			}
		}
		else
		{
			$return = $this->post("contacts", [
				'email' => $request('email'),
				'listIds' => $listIds,
				'updateEnabled' => true,
				'attributes' => $mergeFields
			]);
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
		return isset($formattedResponse['message'])
			? $formattedResponse['message']
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
				'Authorization' => "Bearer {$this->apiKey}",
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
