<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Colors\Src\Listener;

use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Arr;

class FormListener
{
	/**
	 * @var array
	 */
	protected static $types = [
		'button_item',
		'column',
		'description_list',
		'gallery',
		'grid',
		'grid_item',
		'headline',
		'map',
		'panel',
		'panel-slider_item',
		'popover_item',
		'section',
		'table',
		'text',
	];

	public static function addStyleOptions(Config $config, $type)
	{
		if (!in_array($type['name'], self::$types))
		{
			return $type;
		}

		// Section
		if (Arr::has($type, 'fields.style.options'))
		{
			$type['fields']['style']['options']['Tertiary']   = 'tertiary';
			$type['fields']['style']['options']['Quaternary'] = 'quaternary';
			$type['fields']['style']['options']['Quinary']    = 'quinary';
			$type['fields']['style']['options']['Senary']     = 'senary';
		}

		// Headline
		if (Arr::has($type, 'fields.title_color.options'))
		{
			$type['fields']['title_color']['options']['Tertiary']   = 'tertiary';
			$type['fields']['title_color']['options']['Quaternary'] = 'quaternary';
			$type['fields']['title_color']['options']['Quinary']    = 'quinary';
			$type['fields']['title_color']['options']['Senary']     = 'senary';
		}

		// Text
		if (Arr::has($type, 'fields.text_color.options'))
		{
			$type['fields']['text_color']['options']['Tertiary']   = 'tertiary';
			$type['fields']['text_color']['options']['Quaternary'] = 'quaternary';
			$type['fields']['text_color']['options']['Quinary']    = 'quinary';
			$type['fields']['text_color']['options']['Senary']     = 'senary';
		}

		// Button
		if (Arr::has($type, 'fields.button_style.options'))
		{
			$type['fields']['button_style']['options']['Tertiary']   = 'tertiary';
			$type['fields']['button_style']['options']['Quaternary'] = 'quaternary';
			$type['fields']['button_style']['options']['Quinary']    = 'quinary';
			$type['fields']['button_style']['options']['Senary']     = 'senary';
		}

		// Grid Item, Panel Slider Item, Panel
		if (Arr::has($type, 'fields.panel_style.options'))
		{
			$type['fields']['panel_style']['options']['Card Tertiary']   = 'card-tertiary';
			$type['fields']['panel_style']['options']['Card Quaternary'] = 'card-quaternary';
			$type['fields']['panel_style']['options']['Card Quinary']    = 'card-quinary';
			$type['fields']['panel_style']['options']['Card Senary']     = 'card-senary';
			$type['fields']['panel_style']['options']['Tile Tertiary']   = 'tile-tertiary';
			$type['fields']['panel_style']['options']['Tile Quaternary'] = 'tile-quaternary';
			$type['fields']['panel_style']['options']['Tile Quinary']    = 'tile-quinary';
			$type['fields']['panel_style']['options']['Tile Senary']     = 'tile-senary';
		}

		// Popover Item
		if (Arr::has($type, 'fields.card_style.options'))
		{
			$type['fields']['card_style']['options']['Tertiary']   = 'tertiary';
			$type['fields']['card_style']['options']['Quaternary'] = 'quaternary';
			$type['fields']['card_style']['options']['Quinary']    = 'quinary';
			$type['fields']['card_style']['options']['Senary']     = 'senary';
		}

		// Column
		if (Arr::has($type, 'fields.style.options') && $type['name'] == 'column')
		{
			$type['fields']['style']['options']['Card Tertiary']   = 'card-tertiary';
			$type['fields']['style']['options']['Card Quaternary'] = 'card-quaternary';
			$type['fields']['style']['options']['Card Quinary']    = 'card-quinary';
			$type['fields']['style']['options']['Card Senary']     = 'card-senary';
			$type['fields']['style']['options']['Tile Tertiary']   = 'tile-tertiary';
			$type['fields']['style']['options']['Tile Quaternary'] = 'tile-quaternary';
			$type['fields']['style']['options']['Tile Quinary']    = 'tile-quinary';
			$type['fields']['style']['options']['Tile Senary']     = 'tile-senary';

			unset($type['fields']['style']['options']['Tertiary']);
			unset($type['fields']['style']['options']['Quaternary']);
			unset($type['fields']['style']['options']['Quinary']);
			unset($type['fields']['style']['options']['Senary']);
		}

		return $type;
	}
}
