<?php

namespace Kicktemp\YOOaddons\ContactSource\Src\Type;

use function YOOtheme\trans;

class CategoryContactParamsType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'image' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Image'),
                    ],
                ],

                'image_alt' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Image Alt'),
                        'filters' => ['limit'],
                    ],
                ],
            ],
        ];
    }
}
