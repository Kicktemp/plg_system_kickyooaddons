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

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use function YOOtheme\trans;
use Joomla\Component\Banners\Site\Helper\BannerHelper;

class BannerType
{
	/**
	 * @return array
	 */
	public static function config()
	{
		return [

			'fields' => [

				'type' => [
					'type' => 'Int',
					'metadata' => [
						'label' => trans('Banner Type'),
					],
				],

				'track_impressions' => [
					'type' => 'Int',
					'metadata' => [
						'label' => trans('Track Impression'),
					],
				],

				'client_track_impressions' => [
					'type' => 'Int',
					'metadata' => [
						'label' => trans('Client Track Impression'),
					],
				],

				'sticky' => [
					'type' => 'Int',
					'metadata' => [
						'label' => trans('Banner Sticky'),
					],
				],

				'name' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Banner Name'),
						'filters' => ['limit'],
					],
				],

				'description' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Description'),
						'filters' => ['limit'],
					],
				],

				'clickurl' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Click URL'),
						'filters' => ['limit'],
					],
				],

				'link' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Link'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::link',
					],
				],

				'imageurl' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Image'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::imageurl',
					],
				],

				'alt' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Image Alt'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::alt',
					],
				],

				'width' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Image Width'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::width',
					],
				],

				'height' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Image Height'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::height',
					],
				],

				'custombannercode' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Custom Code'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::custombannercode',
					],
				],

				'isImage' => [
					'type' => 'Boolean',
					'metadata' => [
						'label' => trans('is Image'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::isImage',
					],
				],

			],

			'metadata' => [
				'type' => true,
				'label' => 'Banner',
			],

		];
	}

	public static function link($banner)
	{
		return Route::_('index.php?option=com_banners&task=click&id=' . $banner->id);
	}

	public static function isImage($banner)
	{
		$imageurl = $banner->params->get('imageurl');
		return BannerHelper::isImage($imageurl);
	}

	public static function custombannercode($banner)
	{
		$link = self::link($banner);
		return str_replace(array('{CLICKURL}', '{NAME}'), array($link, $banner->name), $banner->custombannercode);
	}

	public static function imageurl($banner)
	{
		$imageurl = $banner->params->get('imageurl');
		$baseurl = strpos($imageurl, 'http') === 0 ? '' : Uri::base();
		return $baseurl . $imageurl;
	}

	public static function width($banner)
	{
		return $banner->params->get('width');
	}

	public static function height($banner)
	{
		return $banner->params->get('height');
	}

	public static function alt($banner)
	{
		return $banner->params->get('alt');
	}
}
