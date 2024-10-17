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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

class ContactsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JController
	 * @since   1.6
	 */
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'image', 'a.image',
				'tag',
			];
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   3.0.1
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = Factory::getApplication();

		// List state information
		$value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$value = $app->input->get('filter_tag', 0, 'uint');
		$this->setState('filter.tag', $value);

		$orderCol = $app->input->get('filter_order', 'a.ordering');

		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}

		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'ASC');

		if (!in_array(strtoupper($listOrder), ['ASC', 'DESC', ''])) {
			$listOrder = 'ASC';
		}

		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);

		$user = Factory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_content')) && (!$user->authorise('core.edit', 'com_contact'))) {
			// Filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language', Multilanguage::isEnabled());

		// Process show_noauth parameter
		if ((!$params->get('show_noauth')) || (!ComponentHelper::getParams('com_contact')->get('show_noauth'))) {
			$this->setState('filter.access', true);
		} else {
			$this->setState('filter.access', false);
		}

		$this->setState('layout', $app->input->getString('layout'));
	}

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
		$id .= ':' . serialize($this->getState('filter.published'));
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.featured');
		$id .= ':' . serialize($this->getState('filter.contact_id'));
		$id .= ':' . $this->getState('filter.contact_id.include');
		$id .= ':' . serialize($this->getState('filter.category_id'));
		$id .= ':' . $this->getState('filter.category_id.include');
		$id .= ':' . serialize($this->getState('filter.author_id'));
		$id .= ':' . $this->getState('filter.author_id.include');
		$id .= ':' . serialize($this->getState('filter.author_alias'));
		$id .= ':' . $this->getState('filter.author_alias.include');
		$id .= ':' . $this->getState('filter.date_filtering');
		$id .= ':' . $this->getState('filter.date_field');
		$id .= ':' . $this->getState('filter.start_date_range');
		$id .= ':' . $this->getState('filter.end_date_range');
		$id .= ':' . $this->getState('filter.relative_date');
		$id .= ':' . serialize($this->getState('filter.tag'));

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
		$user = Factory::getUser();

		// Create a new query object.
		$db = $this->getDatabase();

		/** @var \Joomla\Database\DatabaseQuery $query */
		$query = $db->getQuery(true);

		$nowDate = Factory::getDate()->toSql();

		$query->select($this->getState('list.select', 'a.*'))
			->select($this->getSlugColumn($query, 'a.id', 'a.alias') . ' AS slug')
			->select($this->getSlugColumn($query, 'c.id', 'c.alias') . ' AS catslug')
			->from($db->quoteName('#__contact_details', 'a'))
			// Join on category table.
			->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access')
			->leftJoin($db->quoteName('#__categories', 'c'), 'c.id = a.catid')

			// Join over the categories to get parent category titles
			->select('parent.title AS parent_title, parent.id AS parent_id, parent.path AS parent_route, parent.alias AS parent_alias')
			->leftJoin($db->quoteName('#__categories', 'parent'), 'parent.id = c.parent_id');

        // Filter by state
        $state = $this->getState('filter.published');

        if (is_numeric($state)) {
            $query->where($db->quoteName('a.published') . ' = :published');
            $query->bind(':published', $state, ParameterType::INTEGER);
        } else {
            $query->whereIn($db->quoteName('c.published'), [0,1,2]);
        }

        $params      = $this->getState('params');
		$orderby_sec = $params->get('orderby_sec');

		// Filter by access level.
		if ($this->getState('filter.access', true)) {
			$groups = $this->getState('filter.viewlevels', $user->getAuthorisedViewLevels());
			$query->whereIn($db->quoteName('a.access'), $groups)
				->whereIn($db->quoteName('c.access'), $groups);
		}

		// Filter by a single or group of contacts.
		$articleId = $this->getState('filter.article_id');

		if (is_numeric($articleId)) {
			$articleId = (int) $articleId;
			$type      = $this->getState('filter.article_id.include', true) ? ' = ' : ' <> ';
			$query->where($db->quoteName('a.id') . $type . ':articleId')
				->bind(':articleId', $articleId, ParameterType::INTEGER);
		} elseif (is_array($articleId)) {
			$articleId = ArrayHelper::toInteger($articleId);

			if ($this->getState('filter.article_id.include', true)) {
				$query->whereIn($db->quoteName('a.id'), $articleId);
			} else {
				$query->whereNotIn($db->quoteName('a.id'), $articleId);
			}
		}

		// Filter by a single or group of categories
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId)) {
			$type = $this->getState('filter.category_id.include', true) ? ' = ' : ' <> ';

			// Add subcategory check
			$includeSubcategories = $this->getState('filter.subcategories', false);

			if ($includeSubcategories) {
				$categoryId = (int) $categoryId;
				$levels     = (int) $this->getState('filter.max_category_levels', 1);

				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true)
					->select($db->quoteName('sub.id'))
					->from($db->quoteName('#__categories', 'sub'))
					->join(
						'INNER',
						$db->quoteName('#__categories', 'this'),
						$db->quoteName('sub.lft') . ' > ' . $db->quoteName('this.lft')
						. ' AND ' . $db->quoteName('sub.rgt') . ' < ' . $db->quoteName('this.rgt')
					)
					->where($db->quoteName('this.id') . ' = :subCategoryId');

				$query->bind(':subCategoryId', $categoryId, ParameterType::INTEGER);

				if ($levels >= 0) {
					$subQuery->where($db->quoteName('sub.level') . ' <= ' . $db->quoteName('this.level') . ' + :levels');
					$query->bind(':levels', $levels, ParameterType::INTEGER);
				}

				// Add the subquery to the main query
				$query->where(
					'(' . $db->quoteName('a.catid') . $type . ':categoryId OR ' . $db->quoteName('a.catid') . ' IN (' . $subQuery . '))'
				);
				$query->bind(':categoryId', $categoryId, ParameterType::INTEGER);
			} else {
				$query->where($db->quoteName('a.catid') . $type . ':categoryId');
				$query->bind(':categoryId', $categoryId, ParameterType::INTEGER);
			}
		} elseif (is_array($categoryId) && (count($categoryId) > 0)) {
			$categoryId = ArrayHelper::toInteger($categoryId);

			if (!empty($categoryId)) {
				if ($this->getState('filter.category_id.include', true)) {
					$query->whereIn($db->quoteName('a.catid'), $categoryId);
				} else {
					$query->whereNotIn($db->quoteName('a.catid'), $categoryId);
				}
			}
		}

		// Filter by author
		$authorId    = $this->getState('filter.author_id');
		$authorWhere = '';

		if (is_numeric($authorId)) {
			$authorId    = (int) $authorId;
			$type        = $this->getState('filter.author_id.include', true) ? ' = ' : ' <> ';
			$authorWhere = $db->quoteName('a.created_by') . $type . ':authorId';
			$query->bind(':authorId', $authorId, ParameterType::INTEGER);
		} elseif (is_array($authorId)) {
			$authorId = array_values(array_filter($authorId, 'is_numeric'));

			if ($authorId) {
				$type        = $this->getState('filter.author_id.include', true) ? ' IN' : ' NOT IN';
				$authorWhere = $db->quoteName('a.created_by') . $type . ' (' . implode(',', $query->bindArray($authorId)) . ')';
			}
		}

		// Filter by author alias
		$authorAlias      = $this->getState('filter.author_alias');
		$authorAliasWhere = '';

		if (is_string($authorAlias)) {
			$type             = $this->getState('filter.author_alias.include', true) ? ' = ' : ' <> ';
			$authorAliasWhere = $db->quoteName('a.created_by_alias') . $type . ':authorAlias';
			$query->bind(':authorAlias', $authorAlias);
		} elseif (\is_array($authorAlias) && !empty($authorAlias)) {
			$type             = $this->getState('filter.author_alias.include', true) ? ' IN' : ' NOT IN';
			$authorAliasWhere = $db->quoteName('a.created_by_alias') . $type
				. ' (' . implode(',', $query->bindArray($authorAlias, ParameterType::STRING)) . ')';
		}

		if (!empty($authorWhere) && !empty($authorAliasWhere)) {
			$query->where('(' . $authorWhere . ' OR ' . $authorAliasWhere . ')');
		} elseif (!empty($authorWhere) || !empty($authorAliasWhere)) {
			// One of these is empty, the other is not so we just add both
			$query->where($authorWhere . $authorAliasWhere);
		}

		// Filter by start and end dates.
		if ((!$user->authorise('core.edit.state', 'com_contact')) && (!$user->authorise('core.edit', 'com_contact'))) {
			$query->where(
				[
					'(' . $db->quoteName('a.publish_up') . ' IS NULL OR ' . $db->quoteName('a.publish_up') . ' <= :publishUp)',
					'(' . $db->quoteName('a.publish_down') . ' IS NULL OR ' . $db->quoteName('a.publish_down') . ' >= :publishDown)',
				]
			)
				->bind(':publishUp', $nowDate)
				->bind(':publishDown', $nowDate);
		}

		// Filter by Date Range or Relative Date
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		$dateField     = $db->escape($this->getState('filter.date_field', 'a.created'));

		switch ($dateFiltering) {
			case 'range':
				$startDateRange = $this->getState('filter.start_date_range', '');
				$endDateRange   = $this->getState('filter.end_date_range', '');

				if ($startDateRange || $endDateRange) {
					$query->where($db->quoteName($dateField) . ' IS NOT NULL');

					if ($startDateRange) {
						$query->where($db->quoteName($dateField) . ' >= :startDateRange')
							->bind(':startDateRange', $startDateRange);
					}

					if ($endDateRange) {
						$query->where($db->quoteName($dateField) . ' <= :endDateRange')
							->bind(':endDateRange', $endDateRange);
					}
				}

				break;

			case 'relative':
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where(
					$db->quoteName($dateField) . ' IS NOT NULL AND '
					. $db->quoteName($dateField) . ' >= ' . $query->dateAdd($db->quote($nowDate), -1 * $relativeDate, 'DAY')
				);
				break;

			case 'off':
			default:
				break;
		}

		// Process the filter for list views with user-entered filters
		if (is_object($params) && ($params->get('filter_field') !== 'hide') && ($filter = $this->getState('list.filter'))) {
			// Clean filter variable
			$filter      = StringHelper::strtolower($filter);
			$monthFilter = $filter;
			$hitsFilter  = (int) $filter;
			$textFilter  = '%' . $filter . '%';

			switch ($params->get('filter_field')) {
				case 'author':
					$query->where(
						'LOWER(CASE WHEN ' . $db->quoteName('a.created_by_alias') . ' > ' . $db->quote(' ')
						. ' THEN ' . $db->quoteName('a.created_by_alias') . ' ELSE ' . $db->quoteName('ua.name') . ' END) LIKE :search'
					)
						->bind(':search', $textFilter);
					break;

				case 'hits':
					$query->where($db->quoteName('a.hits') . ' >= :hits')
						->bind(':hits', $hitsFilter, ParameterType::INTEGER);
					break;

				case 'month':
					if ($monthFilter != '') {
						$monthStart = date("Y-m-d", strtotime($monthFilter)) . ' 00:00:00';
						$monthEnd   = date("Y-m-t", strtotime($monthFilter)) . ' 23:59:59';

						$query->where(
							[
								':monthStart <= CASE WHEN a.publish_up IS NULL THEN a.created ELSE a.publish_up END',
								':monthEnd >= CASE WHEN a.publish_up IS NULL THEN a.created ELSE a.publish_up END',
							]
						)
							->bind(':monthStart', $monthStart)
							->bind(':monthEnd', $monthEnd);
					}
					break;

				case 'title':
				default:
					// Default to 'title' if parameter is not valid
					$query->where('LOWER(' . $db->quoteName('a.title') . ') LIKE :search')
						->bind(':search', $textFilter);
					break;
			}
		}

		// Filter by language
		if ($this->getState('filter.language')) {
			$query->whereIn($db->quoteName('a.language'), [Factory::getApplication()->getLanguage()->getTag(), '*'], ParameterType::STRING);
		}

		// Filter by a single or group of tags.
		$tags = (array) $this->getState('filter.tags', []);
		$tagOperator = $this->getState('filter.tag_operator', 'IN');

		if ($tags && $tagOperator === 'IN') {
			$this->setState('filter.tag', $tags);
		}

		$tagId = $this->getState('filter.tag');

		if (is_array($tagId) && count($tagId) === 1) {
			$tagId = current($tagId);
		}

		if (is_array($tagId)) {
			$tagId = ArrayHelper::toInteger($tagId);

			if ($tagId) {
				$subQuery = $db->getQuery(true)
					->select('DISTINCT ' . $db->quoteName('content_item_id'))
					->from($db->quoteName('#__contentitem_tag_map'))
					->where(
						[
							$db->quoteName('tag_id') . ' IN (' . implode(',', $query->bindArray($tagId)) . ')',
							$db->quoteName('type_alias') . ' = ' . $db->quote('com_contact.contact'),
						]
					);

				$query->join(
					'INNER',
					'(' . $subQuery . ') AS ' . $db->quoteName('tagmap'),
					$db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
				);
			}
		} elseif ($tagId = (int) $tagId) {
			$query->join(
				'INNER',
				$db->quoteName('#__contentitem_tag_map', 'tagmap'),
				$db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
				. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_contact.contact')
			)
				->where($db->quoteName('tagmap.tag_id') . ' = :tagId')
				->bind(':tagId', $tagId, ParameterType::INTEGER);
		}

		if ($tags) {
			$tagCount = count($tags);
			$tags = implode(',', $tags);

			if ($tagOperator === 'NOT IN') {
				$query->where(
					"a.id NOT IN (SELECT content_item_id FROM #__contentitem_tag_map WHERE tag_id IN ({$tags}))"
				);
			}

			if ($tagOperator === 'AND') {
				$query->where(
					"(SELECT COUNT(1) FROM #__contentitem_tag_map WHERE tag_id IN ({$tags}) AND content_item_id = a.id) = $tagCount"
				);
			}
		}

		// Filter by featured state
		$featured = $this->getState('filter.featured');

		switch ($featured) {
			case 'hide':
				$query->where($db->quoteName('a.featured') . ' = 0');
				break;

			case 'only':
				$query->where($db->quoteName('a.featured') . ' = 1');
				break;

			case 'show':
			default:
				// Normally we do not discriminate between featured/unfeatured items.
				break;
		}

		// Add the list ordering clause.
		$query->order(
			$db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC'))
		);

		return $query;
	}

	/**
	 * Method to get a list of contacts.
	 *
	 * Overridden to inject convert the attribs field into a Registry object.
	 *
	 * @return  mixed  An array of objects on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItems()
	{
		$items = parent::getItems();

		$taggedItems = [];
		// Convert the parameter fields into objects.
		foreach ($items as $item) {
			$contactParams = new Registry($item->params);
			$item->params = clone $this->getState('params');
			$item->params->merge($contactParams);
			$access = $this->getState('filter.access');

			if ($access) {
				// If the access filter has been set, we already have only the articles this user can view.
				$item->params->set('access-view', true);
			} else {
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				if ($item->catid == 0 || $item->category_access === null) {
					$item->params->set('access-view', in_array($item->access, $groups));
				} else {
					$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}

			// Some contexts may not use tags data at all, so we allow callers to disable loading tag data
			if ($this->getState('load_tags', $item->params->get('show_tags', '1'))) {
				$item->tags = new TagsHelper();
				$taggedItems[$item->id] = $item;
			}

			if (!empty($item->misc)) {
				$item->text = $item->misc;
			}

			$this->buildContactExtendedData($item);
		}

		// Load tags of all items.
        if ($taggedItems) {
			$tagsHelper = new TagsHelper();
            $itemIds = \array_keys($taggedItems);

            foreach ($tagsHelper->getMultipleItemTags('com_content.article', $itemIds) as $id => $tags) {
	            $taggedItems[$id]->tags->itemTags = $tags;
            }
        }

        return $items;
	}

	/**
	 * Load extended data (profile, articles) for a contact
	 *
	 * @param   object  $contact  The contact object
	 *
	 * @return  void
	 */
	protected function buildContactExtendedData($contact)
	{
		$db        = $this->getDatabase();
		$nowDate   = Factory::getDate()->toSql();
		$user      = Factory::getUser();
		$groups    = $user->getAuthorisedViewLevels();
		$published = $this->getState('filter.published');
		$query     = $db->getQuery(true);

		// If we are showing a contact list, then the contact parameters take priority
		// So merge the contact parameters with the merged parameters
		if ($this->getState('params')->get('show_contact_list')) {
			$this->getState('params')->merge($contact->params);
		}

		// Get the com_content articles by the linked user
		if ((int) $contact->user_id && $this->getState('params')->get('show_articles')) {
			$query->select('a.id')
				->select('a.title')
				->select('a.state')
				->select('a.access')
				->select('a.catid')
				->select('a.created')
				->select('a.language')
				->select('a.publish_up')
				->select('a.introtext')
				->select('a.images')
				->select($this->getSlugColumn($query, 'a.id', 'a.alias') . ' AS slug')
				->select($this->getSlugColumn($query, 'c.id', 'c.alias') . ' AS catslug')
				->from($db->quoteName('#__content', 'a'))
				->leftJoin($db->quoteName('#__categories', 'c') . ' ON a.catid = c.id')
				->where($db->quoteName('a.created_by') . ' = :created_by')
				->whereIn($db->quoteName('a.access'), $groups)
				->bind(':created_by', $contact->user_id, ParameterType::INTEGER)
				->order('a.publish_up DESC');

			// Filter per language if plugin published
			if (Multilanguage::isEnabled()) {
				$language = [Factory::getLanguage()->getTag(), $db->quote('*')];
				$query->whereIn($db->quoteName('a.language'), $language, ParameterType::STRING);
			}

			if (is_numeric($published)) {
				$query->where('a.state IN (1,2)')
					->where('(' . $db->quoteName('a.publish_up') . ' IS NULL' .
						' OR ' . $db->quoteName('a.publish_up') . ' <= :now1)')
					->where('(' . $db->quoteName('a.publish_down') . ' IS NULL' .
						' OR ' . $db->quoteName('a.publish_down') . ' >= :now2)')
					->bind([':now1', ':now2'], $nowDate);
			}

			// Number of articles to display from config/menu params
			$articles_display_num = $this->getState('params')->get('articles_display_num', 10);

			// Use contact setting?
			if ($articles_display_num === 'use_contact') {
				$articles_display_num = $contact->params->get('articles_display_num', 10);

				// Use global?
				if ((string) $articles_display_num === '') {
					$articles_display_num = ComponentHelper::getParams('com_contact')->get('articles_display_num', 10);
				}
			}

			$query->setLimit((int) $articles_display_num);
			$db->setQuery($query);
			$contact->articles = $db->loadObjectList();
		} else {
			$contact->articles = null;
		}

		// Get the profile information for the linked user
		$userModel = $this->bootComponent('com_users')->getMVCFactory()
			->createModel('User', 'Administrator', ['ignore_request' => true]);
		$data = $userModel->getItem((int) $contact->user_id);

		PluginHelper::importPlugin('user');

		// Get the form.
		Form::addFormPath(JPATH_SITE . '/components/com_users/forms');

		$form = Form::getInstance('com_users.profile', 'profile');

		// Trigger the form preparation event.
		Factory::getApplication()->triggerEvent('onContentPrepareForm', [$form, $data]);

		// Trigger the data preparation event.
		Factory::getApplication()->triggerEvent('onContentPrepareData', ['com_users.profile', $data]);

		// Load the data into the form after the plugins have operated.
		$form->bind($data);
		$contact->profile = $form;
	}

	/**
	 * Generate column expression for slug or catslug.
	 *
	 * @param   QueryInterface  $query  Current query instance.
	 * @param   string          $id     Column id name.
	 * @param   string          $alias  Column alias name.
	 *
	 * @return  string
	 *
	 * @since   4.0.0
	 */
	private function getSlugColumn($query, $id, $alias)
	{
		return 'CASE WHEN '
			. $query->charLength($alias, '!=', '0')
			. ' THEN '
			. $query->concatenate([$query->castAsChar($id), $alias], ':')
			. ' ELSE '
			. $query->castAsChar($id) . ' END';
	}
}
