<?php

namespace YOOtheme;

return [
    'transforms' => [
        'render' => function ($node) {
            $meta = app(Metadata::class);
            $meta->set('script:form', ['src' => Path::get('../../app/faved.min.js'), 'defer' => true, 'version' => '[VERSION]']);

            // Don't render element if content fields are empty
            return (bool) $node->props['icon'];
        },
    ],
];
