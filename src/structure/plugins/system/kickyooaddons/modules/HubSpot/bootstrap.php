<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\HubSpot;

use Kicktemp\YOOaddons\HubSpot\Src\HubSpotApi;
use Kicktemp\YOOaddons\HubSpot\Src\Listener\LoadCustomizerData;
use YOOtheme\Builder;
use YOOtheme\Config;
use YOOtheme\HttpClientInterface;
use YOOtheme\Path;
use YOOtheme\Translator;

return [

	'events' => [
		'customizer.init' => [LoadCustomizerData::class => ['@handle', 10]],
	],

	'routes' => [
        ['post', '/theme/kickhubspot/forms', HubSpotApi::class . '@forms'],
        ['post', '/theme/kickhubspot/fields', HubSpotApi::class . '@fields'],
        ['post', '/theme/kickhubspot/hubsubmit', HubSpotApi::class . '@submit', ['csrf' => false, 'allowed' => true]],
	],

	'extend' => [

		Builder::class => function (Builder $builder) {
			$builder->addTypePath(Path::get('./elements/*/element.json'));
		},

	],

	'services' => [
        HubSpotApi::class => function (Config $config, HttpClientInterface $client, Translator $translator)
        {
            return new HubSpotApi(
                $config('app.secret'),
                $config('~theme.hubspot_api'),
                $config('~theme.hubspot_portalid'),
                $client,
                $translator
            );
        },
	],
];
