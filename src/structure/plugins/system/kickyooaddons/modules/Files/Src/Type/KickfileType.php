<?php

namespace Kicktemp\YOOaddons\Files\Src\Type;

use function YOOtheme\app;
use YOOtheme\File;
use YOOtheme\Path;
use YOOtheme\Str;
use function YOOtheme\trans;
use YOOtheme\Url;
use YOOtheme\View;

class KickfileType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'name' => [
                    'type' => 'String',
                    'args' => [
                        'title_case' => [
                            'type' => 'Boolean',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Name'),
                        'arguments' => [
                            'title_case' => [
                                'label' => trans('Convert'),
                                'type' => 'checkbox',
                                'text' => trans('Convert to title-case'),
                            ],
                        ],
                        'filters' => ['limit'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::name',
                    ],
                ],

                'basename' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Basename'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::basename',
                    ],
                ],

                'dirname' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Dirname'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::dirname',
                    ],
                ],

                'url' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Url'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::url',
                    ],
                ],

                'path' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Path'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::path',
                    ],
                ],

                'content' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Content'),
                        'filters' => ['limit'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::content',
                    ],
                ],

                'size' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Size'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::size',
                    ],
                ],

                'extension' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Extension'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::extension',
                    ],
                ],

                'mimetype' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Mimetype'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::mimetype',
                    ],
                ],

                'accessed' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Accessed'),
                        'filters' => ['date'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::accessed',
                    ],
                ],

                'changed' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Changed'),
                        'filters' => ['date'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::changed',
                    ],
                ],

                'modified' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Modified'),
                        'filters' => ['date'],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::modified',
                    ],
                ],

                'jcfields' => [
	                'type' => 'String',
	                'args' => [
                        'name' => [
                            'type' => 'String',
                        ],
                    ],
	                'metadata' => [
		                'label' => trans('Custom Field'),
		                'arguments' => [
			                'name' => [
				                'label' => trans('Custom Field Name'),
				                'type' => 'text',
				                'placeholder' => 'Joomla Custom Field Name',
			                ]
		                ],
		                'filters' => ['limit'],
	                ],
	                'extensions' => [
		                'call' => __CLASS__ . '::jcfields',
	                ],
                ],
            ],

            'metadata' => [
                'type' => true,
                'label' => trans('File'),
            ],
        ];
    }

    public static function name($file, $args)
    {
        $name = basename($file['file'], '.' . File::getExtension($file['file']));

        if (!empty($args['title_case'])) {
            $name = Str::titleCase($name);
        }

        return $name;
    }

    public static function content($file)
    {
        return File::getContents($file['file']);
    }

    public static function size($file)
    {
        return app(View::class)->formatBytes(File::getSize($file['file']) ?: 0);
    }

    public static function accessed($file)
    {
        return File::getATime($file['file']);
    }

    public static function changed($file)
    {
        return File::getCTime($file['file']);
    }

    public static function modified($file)
    {
        return File::getMTime($file['file']);
    }

    public static function mimetype($file)
    {
        return File::getMimetype($file['file']);
    }

    public static function extension($file)
    {
        return File::getExtension($file['file']);
    }

    public static function basename($file)
    {
        return basename($file['file']);
    }

    public static function dirname($file)
    {
        return dirname(self::path($file['file']));
    }

    public static function path($file)
    {
        return Path::relative('~', $file['file']);
    }

    public static function url($file)
    {
        return Url::relative(Url::to($file['file']));
    }

	public static function jcfields($file, $args)
	{
		if (!empty($args['name'])) {
			$jcfields = $file['jcfields'];

			foreach ($jcfields as $field)
			{
				if ($field->name === $args['name'])
				{
					if (isset($field->rawvalue))
					{
						return $field->rawvalue;
					}
				}
			}
		}
	}
}
