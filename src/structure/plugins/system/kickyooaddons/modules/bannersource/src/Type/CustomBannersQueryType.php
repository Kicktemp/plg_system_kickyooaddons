<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\BannerSource\Src\Type;

use Kicktemp\Yootheme\BannerSource\BannerHelper;

use function YOOtheme\trans;

class CustomBannersQueryType
{
	/**
	 * @return array
	 */
	public static function config()
	{
		return [
			'fields' => [
				'customBanners' => [
					'type' => [
						'listOf' => 'Banner',
					],

					'args' => [
						'cid' => [
							'type' => 'Int',
						],
						'catid' => [
							'type' => [
								'listOf' => 'String',
							],
						],
						'tag_search' => [
							'type' => 'Boolean',
						],
						'offset' => [
							'type' => 'Int',
						],
						'limit' => [
							'type' => 'Int',
						],
						'ordering' => [
							'type' => 'Boolean',
						],
						'oncePerPage' => [
							'type' => 'String',
						],
						'track_impressions' => [
							'type' => 'Boolean',
						],
					],

					'metadata' => [
						'label' => trans('Custom Banners'),
						'group' => trans('Custom'),
						'fields' => [
							'cid' => [
								'label' => trans('Client'),
								'description' => trans('Select banners only from a single client.'),
								'type' => 'select',
								'default' => 0,
								'options' => [['evaluate' => 'yootheme.builder["banners.clients"]']],
								'attrs' => [
									'multiple' => false,
								],
							],
							'catid' => [
								'label' => trans('Filter by Categories'),
								'type' => 'select',
								'default' => [],
								'options' => [['evaluate' => 'yootheme.builder["banners.categories"]']],
								'attrs' => [
									'multiple' => true,
									'class' => 'uk-height-small',
								],
							],
							'track_impressions' => [
								'label' => trans('Track Impressions'),
								'type' => 'checkbox'
							],
							'tag_search' => [
								'label' => trans('Select by Keyword'),
								'description' => trans('Banner is selected by matching the banner keywords to the current document Keywords.'),
								'type' => 'checkbox'
							],
							'_offset' => [
								'description' => trans(
									'Set the starting point and limit the number of articles.'
								),
								'type' => 'grid',
								'width' => '1-2',
								'fields' => [
									'offset' => [
										'label' => trans('Start'),
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
							'ordering' => [
								'label' => trans('Order, Randomise'),
								'type' => 'checkbox'
							],
							'oncePerPage' => [
								'label' => trans('only once per Page'),
								'type' => 'select',
								'default' => '',
								'options' => [
									'None' => '',
									'Mobile' => 'mobile',
									'Desktop' => 'desktop',
									'Full' => 'full'
								],
							]
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
		return BannerHelper::query($args);
	}
}
