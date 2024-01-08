<?php


namespace YOOtheme;

return [

    'transforms' => [

        'render' => function ($node) {
            $meta = app(Metadata::class);
            $meta->set('script:form', ['src' => Path::get('../../app/slide.min.js'), 'defer' => true, 'version' => '[VERSION]']);
        },

    ],
];
