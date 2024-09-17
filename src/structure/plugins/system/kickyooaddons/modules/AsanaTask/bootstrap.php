<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\AsanaTask;

use Kicktemp\YOOaddons\AsanaTask\Src\AsanaApi;
use YOOtheme\Builder;
use YOOtheme\Config;
use YOOtheme\HttpClientInterface;
use YOOtheme\Path;
use YOOtheme\Translator;

return [

	'events' => [
		'customizer.init' => [Src\Listener\LoadCustomizerData::class => ['@handle', 10]],
	],

	'routes' => [
		['post', '/theme/asana/workspaces', AsanaApi::class . '@workspaces'],
		['post', '/theme/asana/projects', AsanaApi::class . '@projects'],
		['post', '/theme/asana/subscribe', AsanaApi::class . '@subscribe', ['csrf' => false, 'allowed' => true]],
	],

	'extend' => [

		Builder::class => function (Builder $builder) {
			$builder->addTypePath(Path::get('./elements/*/element.json'));
		},

	],

	'services' => [
        AsanaApi::class => function (Config $config, HttpClientInterface $client, Translator $translator)
		{
			return new AsanaApi(
				$config('app.secret'),
				$config('~theme.asana_api'),
				$client,
				$translator
			);
		},
	],
];
