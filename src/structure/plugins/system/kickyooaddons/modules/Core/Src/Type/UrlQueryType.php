<?php

namespace Kicktemp\YOOaddons\Core\Src\Type;

use Joomla\CMS\Uri\Uri;

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
                        'label' => 'Url',
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
