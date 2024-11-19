<?php

namespace Kicktemp\YOOaddons\ContactSource\Src\Type;

use function YOOtheme\trans;

class KickContactQueryType
{
    protected static $view = ['com_contact.contact'];

    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'kickcontact' => [
                    'type' => 'KickContact',

                    'metadata' => [
                        'group' => trans('Page'),
                        'label' => trans('Kick Contact'),
                        'view' => static::$view,
                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root)
    {
        if (in_array($root['template'] ?? '', static::$view)) {
            return $root['item'];
        }
    }
}
