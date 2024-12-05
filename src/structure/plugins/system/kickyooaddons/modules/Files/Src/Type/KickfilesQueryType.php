<?php

namespace Kicktemp\YOOaddons\Files\Src\Type;

use Joomla\CMS\HTML\HTMLHelper;
use YOOtheme\Builder\Joomla\Fields\FieldsHelper;
use YOOtheme\Builder\Source\Filesystem\FileHelper;
use YOOtheme\Path;
use function YOOtheme\app;
use function YOOtheme\trans;

class KickfilesQueryType
{
    /**
     * @param string $rootDir
     *
     * @return array
     */
    public static function config($rootDir)
    {
	    return [
		    'fields' => [
			    'kickfiles' => [
				    'type' => [
					    'listOf' => 'Kickfile',
				    ],

				    'args' => [
					    'pattern' => [
						    'type' => 'String',
					    ],
					    'offset' => [
						    'type' => 'Int',
					    ],
					    'limit' => [
						    'type' => 'Int',
					    ],
					    'order' => [
						    'type' => 'String',
					    ],
					    'order_direction' => [
						    'type' => 'String',
					    ],
				    ],

				    'metadata' => [
					    'label' => trans('Kicktemp Files'),
                        'group' => trans('External'),
					    'fields' => [
						    'pattern' => [
							    'label' => trans('Path Pattern'),
                                'description' => "Pick a folder to load file content dynamically. Alternatively, set a path <a href=\"https://www.php.net/manual/en/function.glob.php\" target=\"_blank\">glob pattern</a> to filter files. For example <code>{$rootDir}/*.{jpg,png}</code>. The path is relative to the system folder and has to be a subdirectory of <code>{$rootDir}</code>. Use <code>{id}</code> for Article ID or <code>{alias}</code> for Article Alias. To get a custom field use <code>{jcf name}</code> or <code>{jcf name,value}</code>, if value is not specified rawvalue is taken.",
                                'type' => 'select-file',
                            ],
						    '_offset' => [
							    'description' => trans(
								    'Set the starting point and limit the number of files.'
							    ),
							    'type' => 'grid',
							    'width' => '1-2',
							    'fields' => [
								    'offset' => [
									    'label' => 'Start',
									    'type' => 'number',
									    'default' => 0,
									    'modifier' => 1,
									    'attrs' => [
										    'min' => 1,
										    'required' => true,
									    ],
								    ],
								    'limit' => [
									    'label' => trans('Quantity'),
									    'type' => 'limit',
									    'default' => 10,
									    'attrs' => [
										    'min' => 1,
									    ],
								    ],
							    ],
						    ],
						    '_order' => [
							    'type' => 'grid',
							    'width' => '1-2',
							    'description' => trans(
								    'The Default order will follow the order set by the brackets or fallback to the default files order set by the system.'
							    ),
							    'fields' => [
								    'order' => [
									    'label' => trans('Order'),
									    'type' => 'select',
									    'default' => 'name',
									    'options' => [
										    trans('Default') => 'default',
										    trans('Alphabetical') => 'name',
										    trans('Random') => 'rand',
									    ],
								    ],
								    'order_direction' => [
									    'label' => trans('Direction'),
									    'type' => 'select',
									    'default' => 'ASC',
									    'options' => [
										    trans('Ascending') => 'ASC',
										    trans('Descending') => 'DESC',
									    ],
									    'enable' => 'order != "rand"',
								    ],
							    ],
						    ],
                        ],
                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],

                ],

            ],

        ];
    }

    public static function resolve($root, array $args)
    {
    	$item = null;
        $context = 'com_content.article';
	    if (isset($root['article'])) {
		    $item = $root['article'];
	    }

        if (isset($root['category'])) {
            $item = $root['category'];
            $context = 'com_content.categories';
        }

	    if (isset($root['item'])) {
		    $item = $root['item'];
	    }

	    if (empty($args['pattern'])) {
		    return [];
	    }

	    $regexjcf = '/{jcf\s(.*?)}/i';
	    // Find all instances of plugin and put in $matchesmod for loadmodule
	    preg_match_all($regexjcf, $args['pattern'], $matchesjcf, PREG_SET_ORDER);

	    if ($matchesjcf)
	    {
	    	if (!isset($item->jcfields))
		    {
			    $item->jcfields = FieldsHelper::getFields($context, $item);
		    }

		    foreach ($matchesjcf as $matchjcf)
		    {
			    $matchesjcflist = explode(',', $matchjcf[1]);

			    // We may not have a specific module so set to null
			    if (!array_key_exists(1, $matchesjcflist))
			    {
				    $matchesjcflist[1] = 'rawvalue';
			    }

			    $output = '';

			    foreach ($item->jcfields as $field)
			    {
				    if ($field->name === $matchesjcflist[0])
				    {
				    	$value = $matchesjcflist[1];
				    	if (isset($field->$value))
					    {
					        $output = $field->$value;
					    }
				    }
			    }

			    if (($start = strpos($args['pattern'], $matchjcf[0])) !== false)
			    {
				    $args['pattern'] = substr_replace($args['pattern'], $output, $start, strlen($matchjcf[0]));
			    }
		    }
	    }

        $images  = json_decode($item->images);

	    if (!is_null($item) && isset($item->id) && !empty($args['pattern']))
	    {
		    $args['pattern'] = str_replace('{id}', $item->id, $args['pattern']);
	    }

	    if (!is_null($item) && isset($item->alias) && !empty($args['pattern']))
	    {
		    $args['pattern'] = str_replace('{alias}', $item->alias, $args['pattern']);
	    }

        if (!is_null($item) && isset($images->image_intro) && $images->image_intro !== '' && !empty($args['pattern']))
        {
            $intro_image = HTMLHelper::_('cleanImageURL', $images->image_intro);
            $intro_path = Path::parse($intro_image->url);
            $args['pattern'] = str_replace('{intro_image}', $intro_path['dirname'], $args['pattern']);
        }

        if (!is_null($item) && isset($images->image_fulltext) && $images->image_fulltext !== '' && !empty($args['pattern']))
        {
            $image_fulltext = HTMLHelper::_('cleanImageURL', $images->image_fulltext);
            $fulltext_path = Path::parse($image_fulltext->url);
            $args['pattern'] = str_replace('{image_fulltext}', $fulltext_path['dirname'], $args['pattern']);
        }

		$files = [];
		$images = app(FileHelper::class)->query($args);

		foreach ($images as $key => $image){
				$files[$key]['file'] = $image;
				$files[$key]['jcfields'] = (isset($item->jcfields)) ? $item->jcfields : null;
		}

        return $files;
    }
}
