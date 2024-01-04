<?php

namespace YOOtheme;

use Kicktemp\YOOaddons\Brevo\Src\BrevoApi;

return [

    'transforms' => [

        'render' => function ($node) {

            /**
             * @var BrevoApi $controller
             * @var Metadata  $meta
             */
            list($controller, $meta) = app(BrevoApi::class, Metadata::class);

            $provider = (array) $node->props['provider'];

            $listids = array();

            foreach(array_filter($node->props['list_ids']) as $folder => $lists)
            {
            	foreach ($lists as $list)
	            {
	            	$listids[] = $list;
	            }
            }

            $brevo = array(
            	"includeListIds" => $listids,
            	"templateId" => $node->props['template_id'],
            	"optIn" => $node->props['double_optin'],
            	"redirectionUrl" => $node->props['redirection_url']
            );

	        $node->settings = $controller->encodeData(
		        array_merge($provider, $brevo)
	        );

	        $node->form = [
		        'action' => Url::route('theme/brevo/subscribe', [
			        'hash' => $controller->getHash($node->settings),
		        ]),
	        ];

            $meta->set('script:brevo', ['src' => Path::get('../../app/brevo.min.js'), 'defer' => true]);
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
