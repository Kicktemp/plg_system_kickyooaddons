<?php

namespace YOOtheme;

use Kicktemp\YOOaddons\RapidMail\Src\RapidmailApi;

return [

    'transforms' => [

        'render' => function ($node) {

            /**
             * @var RapidmailApi $controller
             * @var Metadata  $meta
             */
            list($controller, $meta) = app(RapidmailApi::class, Metadata::class);

            $provider = (array) $node->props['provider'];

			$rapidmail = array(
		        "recipientlist_id" => $node->props['list_id'],
		        "optIn" => $node->props['double_optin'],
	        );

	        $node->settings = $controller->encodeData(
		        array_merge($provider, $rapidmail)
	        );

	        $node->form = [
		        'action' => Url::route('theme/rapidmail/subscribe', [
			        'hash' => $controller->getHash($node->settings),
		        ]),
	        ];

	        $meta->set('script:rapidmail', [
		        'src' => Path::get('../../app/rapidmail.min.js'),
		        'defer' => true,
	        ]);
        },

    ],

    'updates' => [

        '1.22.0-beta.0.1' => function ($node) {

            if (isset($node->props['gutter'])) {
                $node->props['gap'] = $node->props['gutter'];
                unset($node->props['gutter']);
            }

        },

        '1.20.0-beta.1.1' => function ($node) {

            if (isset($node->props['maxwidth_align'])) {
                $node->props['block_align'] = $node->props['maxwidth_align'];
                unset($node->props['maxwidth_align']);
            }

        },

    ],

];
