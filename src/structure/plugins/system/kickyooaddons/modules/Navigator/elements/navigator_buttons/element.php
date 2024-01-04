<?php

namespace YOOtheme;

return [
    'transforms' => [
        'render' => function ($node, $params) {
            // Don't render element if content fields are empty
            return $node->props['content'] != '' ||
                $node->props['type'] ||
                $node->props['modalid'] ||
                $node->props['icon'] ||
                $node->props['link'];
        },
    ],
];
