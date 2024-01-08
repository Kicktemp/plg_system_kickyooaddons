<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\SectionSwitcher\Src\Listener;

use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Arr;

class FormListener
{
	public static function addFormPanel(Config $config, $type)
	{
        // constraint types
        if (!in_array($type['name'], ['section', 'row'])) {
            return $type;
        }

        // make sure the main fieldset is set
        if (!Arr::has($type, 'fieldset.default')) {
            return $type;
        }

        $tabs = array_reduce($type['fieldset']['default']['fields'], function ($carry, $v) {
            return array_merge($carry, [$v['title'] ?? null]);
        }, []);

        if (($index = array_search('Advanced', $tabs)) === false) {
            return $type;
        }

        if ($type['name'] === 'section') {
            $statusField = [
                'type' => 'checkbox',
                'name' => 'kicktemp_switcher.state',
                'label' => 'Kicktemp Switcher',
                'text' => 'Enable as Switcher Area'
            ];

            $configButton = [
                'name' => '_kicktemp_switcher',
                'text' => 'Configuration',
                'type' => 'button-panel',
                'panel' => 'kicktemp-switcher-config',
                'enable' => 'kicktemp_switcher.state',
                'description' => 'Enable this element as a Switcher, and set the configuration.'
            ];

            // set button right after status field
            Arr::splice($type['fieldset']['default']['fields'][$index]['fields'], 2, 0, [$statusField, $configButton]);
        }

        if ($type['name'] === 'row') {
            $statusField = [
                'name' => 'kickswitcher_title',
                'label' => 'Kicktemp Switcher Title',
                'source' => true
            ];


            // set button right after status field
            Arr::splice($type['fieldset']['default']['fields'][$index]['fields'], 2, 0, [$statusField]);
        }

        return $type;
	}
}
