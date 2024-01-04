<?php

namespace YOOtheme;

return [

    'transforms' => [

        'render' => function ($node) {
	        $node->props['title'] = $node->props['hubspot_form'];
            // Don't render element if content fields are empty
            return $node->props['title']
                || $node->props['type'];

        },

    ],

];
