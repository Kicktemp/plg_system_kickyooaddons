<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\ContactSource\Src;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class ContactHelper
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
		$model = new ContactsModel(['ignore_request' => true]);
		$model->setState('params', ComponentHelper::getParams('com_contact'));
		$model->setState('filter.access', true);
		$model->setState('filter.published', 1);
		$model->setState('filter.language', Multilanguage::isEnabled());
		$model->setState('filter.subcategories', false);

		$args += [
			'contact_operator' => 'IN',
			'cat_operator' => 'IN',
			'tag_operator' => 'IN',
			'users_operator' => 'IN',
		];

		if (!empty($args['order'])) {
			if ($args['order'] === 'rand') {
				$args['order'] = Factory::getDbo()
					->getQuery(true)
					->Rand();
			} else {
				$args['order'] = "a.{$args['order']}";
			}
		}

		$props = [
			'offset' => 'list.start',
			'limit' => 'list.limit',
			'order' => 'list.ordering',
			'order_direction' => 'list.direction',
			'order_alphanum' => 'list.alphanum',
			'featured' => 'filter.featured',
			'subcategories' => 'filter.subcategories',
			'tags' => 'filter.tags',
			'tag_operator' => 'filter.tag_operator',
		];

		foreach (array_intersect_key($props, $args) as $key => $prop) {
			$model->setState($prop, $args[$key]);
		}

		if (!empty($args['article'])) {
			$model->setState('filter.contact_id', (array) $args['contact']);
			$model->setState('filter.contact_id.include', $args['contact_operator'] === 'IN');
		}

		if (!empty($args['catid'])) {
			$model->setState('filter.category_id', (array) $args['catid']);
			$model->setState('filter.category_id.include', $args['cat_operator'] === 'IN');
		}

		if (!empty($args['users'])) {
			$model->setState('filter.author_id', (array) $args['users']);
			$model->setState('filter.author_id.include', $args['users_operator'] === 'IN');
		}

		return $model->getItems();
	}
}
