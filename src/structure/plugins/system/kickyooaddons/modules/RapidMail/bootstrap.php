<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\RapidMail;

use Kicktemp\YOOaddons\RapidMail\Src\RapidmailApi;
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
        ['post', '/theme/rapidmail/recipientlists',RapidmailApi::class . '@recipientlists'],
        ['post', '/theme/rapidmail/subscribe', RapidmailApi::class . '@subscribe', ['csrf' => false, 'allowed' => true]],
    ],

	'extend' => [

		Builder::class => function (Builder $builder) {
			$builder->addTypePath(Path::get('./elements/*/element.json'));
		},

	],

	'services' => [
        RapidmailApi::class => function (Config $config, HttpClientInterface $client, Translator $translator) {
            return new RapidmailApi(
                $config('~theme.rapidmail_user'),
                $config('~theme.rapidmail_password'),
                $config('app.secret'),
                $client,
                $translator
            );
        },
	],
];
