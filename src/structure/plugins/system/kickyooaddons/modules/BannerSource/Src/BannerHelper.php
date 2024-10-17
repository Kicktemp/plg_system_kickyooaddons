<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\BannerSource\Src;

use Joomla\CMS\Environment\Browser;
use Joomla\Component\Banners\Administrator\Helper\BannersHelper as BannersComponentHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class BannerHelper
{
	/**
	 * @var array
	 * @since 1.0.1
	 */
	public static $excludeIds = [];

	/**
	 * @param          $ids
	 * @param   array  $args
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get($ids, array $args = [])
	{
		return $ids ? static::query(['article' => (array) $ids] + $args) : [];
	}


	/**
	 * @param   array  $args
	 *
	 * @return array
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public static function query(array $args = [])
	{
		$app = Factory::getApplication();

		BannersComponentHelper::updateReset();
		$model = new BannersModel(['ignore_request' => true]);
		$keywords = explode(',', Factory::getApplication()->getDocument()->getMetaData('keywords'));

		$model->setState('filter.language', $app->getLanguageFilter());

		$props = [
			'offset' => 'list.start',
			'limit' => 'list.limit'
		];

		foreach (array_intersect_key($props, $args) as $key => $prop) {
			$model->setState($prop, $args[$key]);
		}

		if (!empty($args['ordering']) && $args['ordering']) {
			$model->setState('filter.ordering', 'random');
		}

		if (!empty($args['tag_search'])) {
			$model->setState('filter.tag_search', $args['tag_search']);
		}

		if (!empty($args['cid'])) {
			$model->setState('filter.client_id', (int) $args['cid']);
		}

		if (!empty($args['catid'])) {
			$model->setState('filter.category_id', (array) $args['catid']);
		}

		if (!empty($args['oncePerPage'])
			&& $args['oncePerPage']
			&& isset(self::$excludeIds[$args['oncePerPage']])
			&& count($exclude = self::$excludeIds[$args['oncePerPage']])) {

			$model->setState('filter.banner_id', (array) $exclude);
			$model->setState('filter.banner_id.include', false);
		}

		$model->setState('filter.keywords', $keywords);

		$banners = $model->getItems();

		if ($banners && !empty($args['track_impressions']) && $args['track_impressions']) {
			$model->impress();
		}

		if (!empty($args['oncePerPage']) && $args['oncePerPage']) {
			if (!isset(self::$excludeIds[$args['oncePerPage']])) {
				self::$excludeIds[$args['oncePerPage']] = [];
			}

			foreach ($banners as $banner)
			{
				if (!in_array($banner->id, self::$excludeIds[$args['oncePerPage']])) {
					self::$excludeIds[$args['oncePerPage']][] = $banner->id;
				}
			}
		}

		return $banners;
	}
}
