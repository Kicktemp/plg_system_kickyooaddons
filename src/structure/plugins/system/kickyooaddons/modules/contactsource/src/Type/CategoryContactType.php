<?php

namespace Kicktemp\YOOaddons\ContactSource\Src\Type;

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Tree\NodeInterface;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Kicktemp\Yootheme\ContactSource\ContactHelper;
use YOOtheme\Builder\Joomla\Source\TagHelper;
use YOOtheme\Path;
use YOOtheme\View;
use function YOOtheme\app;
use function YOOtheme\trans;

class CategoryContactType
{
	/**
	 * @return array
	 */
	public static function config()
	{
		return [
			'fields' => [
				'title' => [
					'type'     => 'String',
					'metadata' => [
						'label'   => trans('Title'),
						'filters' => ['limit'],
					],
				],

				'description' => [
					'type'     => 'String',
					'metadata' => [
						'label'   => trans('Description'),
						'filters' => ['limit'],
					],
				],

				'numitems' => [
					'type'     => 'String',
					'metadata' => [
						'label' => trans('Article Count'),
					],
				],

				'params' => [
					'type'       => 'CategoryContactParams',
					'metadata'   => [
						'label' => '',
					],
					'extensions' => [
						'call' => __CLASS__ . '::params',
					],
				],

				'link' => [
					'type'       => 'String',
					'metadata'   => [
						'label' => trans('Link'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::link',
					],
				],

				'tagString' => [
					'type'       => 'String',
					'args'       => [
						'parent_id'  => [
							'type' => 'String',
						],
						'separator'  => [
							'type' => 'String',
						],
						'show_link'  => [
							'type' => 'Boolean',
						],
						'link_style' => [
							'type' => 'String',
						],
					],
					'metadata'   => [
						'label'     => trans('Tags'),
						'arguments' => [
							'parent_id'  => [
								'label'       => trans('Parent Tag'),
								'description' => trans(
									'Tags are only loaded from the selected parent tag.'
								),
								'type'        => 'select',
								'default'     => '0',
								'options'     => [
									['value' => '0', 'text' => trans('Root')],
									['evaluate' => 'yootheme.builder.tags'],
								],
							],
							'separator'  => [
								'label'       => trans('Separator'),
								'description' => trans('Set the separator between tags.'),
								'default'     => ', ',
							],
							'show_link'  => [
								'label'   => trans('Link'),
								'type'    => 'checkbox',
								'default' => true,
								'text'    => trans('Show link'),
							],
							'link_style' => [
								'label'       => trans('Link Style'),
								'description' => trans('Set the link style.'),
								'type'        => 'select',
								'default'     => '',
								'options'     => [
									'Default' => '',
									'Muted'   => 'link-muted',
									'Text'    => 'link-text',
									'Heading' => 'link-heading',
									'Reset'   => 'link-reset',
								],
								'enable'      => 'arguments.show_link',
							],
						],
					],
					'extensions' => [
						'call' => __CLASS__ . '::tagString',
					],
				],

				'parent' => [
					'type'       => 'CategoryContact',
					'metadata'   => [
						'label' => trans('Parent Category'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::parent',
					],
				],

				'categories' => [
					'type'       => [
						'listOf' => 'CategoryContact',
					],
					'metadata'   => [
						'label' => trans('Child Categories'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::categories',
					],
				],

				'contacts' => [
					'type'       => [
						'listOf' => 'KickContact',
					],
					'args'       => [
						'subcategories'   => [
							'type' => 'Boolean',
						],
						'offset'          => [
							'type' => 'Int',
						],
						'limit'           => [
							'type' => 'Int',
						],
						'order'           => [
							'type' => 'String',
						],
						'order_direction' => [
							'type' => 'String',
						],
						'order_alphanum'  => [
							'type' => 'Boolean',
						],
					],
					'metadata'   => [
						'label'      => trans('Contacts'),
						'arguments'  => [
							'subcategories'  => [
								'label' => trans('Filter'),
								'text'  => trans('Include articles from child categories'),
								'type'  => 'checkbox',
							],
							'_offset'        => [
								'description' => trans(
									'Set the starting point and limit the number of articles.'
								),
								'type'        => 'grid',
								'width'       => '1-2',
								'fields'      => [
									'offset' => [
										'label'    => trans('Start'),
										'type'     => 'number',
										'default'  => 0,
										'modifier' => 1,
										'attrs'    => [
											'min'      => 1,
											'required' => true,
										],
									],
									'limit'  => [
										'label'   => trans('Quantity'),
										'type'    => 'limit',
										'default' => 10,
										'attrs'   => [
											'min' => 1,
										],
									],
								],
							],
							'_order'         => [
								'type'   => 'grid',
								'width'  => '1-2',
								'fields' => [
									'order'           => [
										'label'   => trans('Order'),
										'type'    => 'select',
										'default' => 'publish_up',
										'options' => [['evaluate' => 'yootheme.builder["contacts.contactOrderOptions"]']],
									],
									'order_direction' => [
										'label'   => trans('Direction'),
										'type'    => 'select',
										'default' => 'DESC',
										'options' => [
											trans('Ascending')  => 'ASC',
											trans('Descending') => 'DESC',
										],
									],
								],
							],
							'order_alphanum' => [
								'text' => trans('Alphanumeric Ordering'),
								'type' => 'checkbox',
							],
						],
						'directives' => [],
					],
					'extensions' => [
						'call' => __CLASS__ . '::contacts',
					],
				],

				'tags' => [
					'type'       => [
						'listOf' => 'Tag',
					],
					'args'       => [
						'parent_id' => [
							'type' => 'String',
						],
					],
					'metadata'   => [
						'label'  => trans('Tags'),
						'fields' => [
							'parent_id' => [
								'label'       => trans('Parent Tag'),
								'description' => trans(
									'Tags are only loaded from the selected parent tag.'
								),
								'type'        => 'select',
								'default'     => '0',
								'options'     => [
									['value' => '0', 'text' => trans('Root')],
									['evaluate' => 'yootheme.builder.tags'],
								],
							],
						],
					],
					'extensions' => [
						'call' => __CLASS__ . '::tags',
					],
				],

				'id' => [
					'type'     => 'String',
					'metadata' => [
						'label' => trans('ID'),
					],
				],
			],

			'metadata' => [
				'type'  => true,
				'label' => trans('Category Contact'),
			],
		];
	}

	public static function params($category)
	{
		return is_string($category->params) ? json_decode($category->params) : $category->params;
	}

	public static function link($category)
	{
		return RouteHelper::getCategoryRoute($category->id, $category->language);
	}

	/**
	 * @param   CategoryNode  $category
	 *
	 * @return NodeInterface|null
	 */
	public static function parent($category)
	{
		return $category->getParent();
	}

	/**
	 * @param   CategoryNode  $category
	 *
	 * @return CategoryNode[]
	 */
	public static function categories($category)
	{
		return $category->getChildren();
	}

	public static function contacts($category, $args)
	{
		$args['catid'] = $category->id;

		return ContactHelper::query($args);
	}

	public static function tags($category, $args)
	{
		if (!isset($category->tags))
		{
			return (new TagsHelper())->getItemTags('com_contact.category', $category->id);
		}
		$tags = $category->tags->itemTags;

		if (!empty($args['parent_id']))
		{
			return TagHelper::filterTags($tags, $args['parent_id']);
		}

		return $tags;
	}

	public static function tagString($category, array $args)
	{
		$tags = static::tags($category, $args);
		$args += [
			'separator'  => ', ',
			'show_link'  => true,
			'link_style' => '',
		];

		return app(View::class)->render(
			Path::get('../../templates/tags'),
			compact('category', 'tags', 'args')
		);
	}
}
