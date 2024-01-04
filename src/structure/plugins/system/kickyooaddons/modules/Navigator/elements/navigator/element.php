<?php

namespace YOOtheme;

return [
    'transforms' => [
        'render' => function ($node) {
	        app(Metadata::class)->set('script:kick-navigator', [
		        'src' => Path::get('./app/navigator.min.js'),
		        'defer' => true,
	        ]);

	        if ($node->props['full_modifier']) {
		        $node->props['modal_center'] = false;
		        $node->props['modal_container'] = false;
		        $node->props['close_style'] = 'full uk-close-large';
	        }
        },
    ],
];
