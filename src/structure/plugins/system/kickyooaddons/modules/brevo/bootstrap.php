<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Brevo;

use Kicktemp\YOOaddons\Brevo\Src\BrevoApi;
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
		['post', '/theme/brevo/checkapi', BrevoApi::class . '@checkAPI'],
		['post', '/theme/brevo/list', BrevoApi::class . '@lists'],
		['post', '/theme/brevo/folder', BrevoApi::class . '@folders'],
		['post', '/theme/brevo/template', BrevoApi::class . '@templates'],
		['post', '/theme/brevo/subscribe', BrevoApi::class . '@subscribe', ['csrf' => false, 'allowed' => true]],
	],

	'extend' => [

		Builder::class => function (Builder $builder) {
			$builder->addTypePath(Path::get('./elements/*/element.json'));
		},

	],

	'services' => [
        BrevoApi::class => function (Config $config, HttpClientInterface $client, Translator $translator)
		{
			return new BrevoApi(
				$config('app.secret'),
				$config('~theme.brevo_api'),
				$client,
				$translator
			);
		},
	],
];
