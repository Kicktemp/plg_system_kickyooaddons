<?php

namespace Kicktemp\YOOaddons\Form\Src\Type;

use Joomla\CMS\Uri\Uri;
use function YOOtheme\trans;

class UrlQueryType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [

            'fields' => [

                'url' => [
                    'type' => 'Url',
                    'metadata' => [
                        'label' => trans('Website Url'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],

            ],

        ];
    }

    public static function resolve()
    {
        $uri = Uri::getInstance();

        return [
            'base' => $uri->base(),
            'root' => $uri->root(),
            'current' => $uri->current(),
        ];
    }
}
