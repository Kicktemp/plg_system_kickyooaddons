<?php

namespace YOOtheme;

return [
    'transforms' => [
        'render' => function ($node, $params) {

            // Don't render element if content fields are empty
            return $node->props['title'] != '' ||
                $node->props['meta'] != '' ||
                $node->props['content'] != '' ||
                $node->props['image'] ||
                $node->props['icon'] ||
                $node->props['link'];
        },
    ],
];
