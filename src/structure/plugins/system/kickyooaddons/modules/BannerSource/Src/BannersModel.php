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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Component\Banners\Site\Model\BannersModel as BaseModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;

class BannersModel extends BaseModel
{
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . serialize($this->getState('filter.banner_id'));
		$id .= ':' . $this->getState('filter.banner_id.include');

		return parent::getStoreId($id);
	}

	/**
	 * Method to get a DatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  DatabaseQuery   A DatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$db         = $this->getDatabase();
		$query      = $db->getQuery(true);
		$ordering   = $this->getState('filter.ordering');
		$tagSearch  = $this->getState('filter.tag_search');
		$cid        = $this->getState('filter.client_id');
		$categoryId = $this->getState('filter.category_id');
		$keywords   = $this->getState('filter.keywords');
		$randomise  = ($ordering === 'random');
		$nowDate    = Factory::getDate()->toSql();

		$query->select(
			[
				$db->quoteName('a.id'),
				$db->quoteName('a.type'),
				$db->quoteName('a.name'),
				$db->quoteName('a.clickurl'),
				$db->quoteName('a.sticky'),
				$db->quoteName('a.cid'),
				$db->quoteName('a.description'),
				$db->quoteName('a.params'),
				$db->quoteName('a.custombannercode'),
				$db->quoteName('a.track_impressions'),
				$db->quoteName('cl.track_impressions', 'client_track_impressions'),
			]
		)
			->from($db->quoteName('#__banners', 'a'))
			->join('LEFT', $db->quoteName('#__banner_clients', 'cl'), $db->quoteName('cl.id') . ' = ' . $db->quoteName('a.cid'))
			->where($db->quoteName('a.state') . ' = 1')
			->extendWhere(
				'AND',
				[
					$db->quoteName('a.publish_up') . ' IS NULL',
					$db->quoteName('a.publish_up') . ' <= :nowDate1',
				],
				'OR'
			)
			->extendWhere(
				'AND',
				[
					$db->quoteName('a.publish_down') . ' IS NULL',
					$db->quoteName('a.publish_down') . ' >= :nowDate2',
				],
				'OR'
			)
			->extendWhere(
				'AND',
				[
					$db->quoteName('a.imptotal') . ' = 0',
					$db->quoteName('a.impmade') . ' < ' . $db->quoteName('a.imptotal'),
				],
				'OR'
			)
			->bind([':nowDate1', ':nowDate2'], $nowDate);

		if ($cid) {
			$query->where(
				[
					$db->quoteName('a.cid') . ' = :clientId',
					$db->quoteName('cl.state') . ' = 1',
				]
			)
				->bind(':clientId', $cid, ParameterType::INTEGER);
		}


		// Filter by a single or group of articles.
		$bannerId = $this->getState('filter.banner_id');

		if (is_numeric($bannerId)) {
			$bannerId = (int) $bannerId;
			$type      = $this->getState('filter.banner_id.include', true) ? ' = ' : ' <> ';
			$query->where($db->quoteName('a.id') . $type . ':bannerId')
				->bind(':bannerId', $bannerId, ParameterType::INTEGER);
		} elseif (is_array($bannerId)) {
			$bannerId = ArrayHelper::toInteger($bannerId);

			if ($this->getState('filter.banner_id.include', true)) {
				$query->whereIn($db->quoteName('a.id'), $bannerId);
			} else {
				$query->whereNotIn($db->quoteName('a.id'), $bannerId);
			}
		}

		// Filter by a single or group of categories
		if (is_numeric($categoryId)) {
			$categoryId = (int) $categoryId;
			$type = $this->getState('filter.category_id.include', true) ? ' = ' : ' <> ';

			// Add subcategory check
			if ($this->getState('filter.subcategories', false)) {
				$levels = (int) $this->getState('filter.max_category_levels', '1');

				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select($db->quoteName('sub.id'))
					->from($db->quoteName('#__categories', 'sub'))
					->join(
						'INNER',
						$db->quoteName('#__categories', 'this'),
						$db->quoteName('sub.lft') . ' > ' . $db->quoteName('this.lft')
						. ' AND ' . $db->quoteName('sub.rgt') . ' < ' . $db->quoteName('this.rgt')
					)
					->where(
						[
							$db->quoteName('this.id') . ' = :categoryId1',
							$db->quoteName('sub.level') . ' <= ' . $db->quoteName('this.level') . ' + :levels',
						]
					);

				// Add the subquery to the main query
				$query->extendWhere(
					'AND',
					[
						$db->quoteName('a.catid') . $type . ':categoryId2',
						$db->quoteName('a.catid') . ' IN (' . $subQuery . ')',
					],
					'OR'
				)
					->bind([':categoryId1', ':categoryId2'], $categoryId, ParameterType::INTEGER)
					->bind(':levels', $levels, ParameterType::INTEGER);
			} else {
				$query->where($db->quoteName('a.catid') . $type . ':categoryId')
					->bind(':categoryId', $categoryId, ParameterType::INTEGER);
			}
		} elseif (is_array($categoryId) && (count($categoryId) > 0)) {
			$categoryId = ArrayHelper::toInteger($categoryId);

			if ($this->getState('filter.category_id.include', true)) {
				$query->whereIn($db->quoteName('a.catid'), $categoryId);
			} else {
				$query->whereNotIn($db->quoteName('a.catid'), $categoryId);
			}
		}

		if ($tagSearch) {
			if (!$keywords) {
				// No keywords, select nothing.
				$query->where('0 != 0');
			} else {
				$temp   = array();
				$config = ComponentHelper::getParams('com_banners');
				$prefix = $config->get('metakey_prefix');

				if ($categoryId) {
					$query->join('LEFT', $db->quoteName('#__categories', 'cat'), $db->quoteName('a.catid') . ' = ' . $db->quoteName('cat.id'));
				}

				foreach ($keywords as $key => $keyword) {
					$regexp       = '[[:<:]]' . $keyword . '[[:>:]]';
					$valuesToBind = [$keyword, $keyword, $regexp];

					if ($cid) {
						$valuesToBind[] = $regexp;
					}

					if ($categoryId) {
						$valuesToBind[] = $regexp;
					}

					// Because values to $query->bind() are passed by reference, using $query->bindArray() here instead to prevent overwriting.
					$bounded = $query->bindArray($valuesToBind, ParameterType::STRING);

					$condition1 = $db->quoteName('a.own_prefix') . ' = 1'
						. ' AND ' . $db->quoteName('a.metakey_prefix')
						. ' = SUBSTRING(' . $bounded[0] . ',1,LENGTH(' . $db->quoteName('a.metakey_prefix') . '))'
						. ' OR ' . $db->quoteName('a.own_prefix') . ' = 0'
						. ' AND ' . $db->quoteName('cl.own_prefix') . ' = 1'
						. ' AND ' . $db->quoteName('cl.metakey_prefix')
						. ' = SUBSTRING(' . $bounded[1] . ',1,LENGTH(' . $db->quoteName('cl.metakey_prefix') . '))'
						. ' OR ' . $db->quoteName('a.own_prefix') . ' = 0'
						. ' AND ' . $db->quoteName('cl.own_prefix') . ' = 0'
						. ' AND ' . ($prefix == substr($keyword, 0, strlen($prefix)) ? '0 = 0' : '0 != 0');

					$condition2 = $db->quoteName('a.metakey') . ' ' . $query->regexp($bounded[2]);

					if ($cid) {
						$condition2 .= ' OR ' . $db->quoteName('cl.metakey') . ' ' . $query->regexp($bounded[3]) . ' ';
					}

					if ($categoryId) {
						$condition2 .= ' OR ' . $db->quoteName('cat.metakey') . ' ' . $query->regexp($bounded[4]) . ' ';
					}

					$temp[] = "($condition1) AND ($condition2)";
				}

				$query->where('(' . implode(' OR ', $temp) . ')');
			}
		}

		// Filter by language
		if ($this->getState('filter.language')) {
			$query->whereIn($db->quoteName('a.language'), [Factory::getLanguage()->getTag(), '*'], ParameterType::STRING);
		}

		$query->order($db->quoteName('a.sticky') . ' DESC, ' . ($randomise ? $query->rand() : $db->quoteName('a.ordering')));

		return $query;
	}
}
