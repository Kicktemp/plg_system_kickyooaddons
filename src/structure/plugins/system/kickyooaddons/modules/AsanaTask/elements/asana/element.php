<?php

namespace YOOtheme;

use Kicktemp\YOOaddons\AsanaTask\Src\AsanaApi;

return [

    'transforms' => [

        'render' => function ($node) {

            /**
             * @var AsanaApi $controller
             * @var Metadata  $meta
             */
            list($controller, $meta) = app(AsanaApi::class, Metadata::class);

            $provider = (array) $node->props['provider'];

            $asanatask = array(
            	"projects" => $node->props['project'],
            	"workspace" => $node->props['workspace']
            );

	        $node->settings = $controller->encodeData(
		        array_merge($provider, $asanatask)
	        );

	        $node->form = [
		        'action' => Url::route('theme/asana/subscribe', [
			        'hash' => $controller->getHash($node->settings),
		        ]),
	        ];

            $meta->set('script:asana', ['src' => Path::get('../../app/asana.min.js'), 'defer' => true]);
        },

    ],

];
