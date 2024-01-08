<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\SectionSlideshow\Src\Listener;

use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Arr;

class FormListener
{
	public static function addFormPanel(Config $config, $type)
	{
        // constraint types
        if (!in_array($type['name'], ['section'])) {
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

        $statusField = [
            'type' => 'checkbox',
            'name' => 'kicktemp_slideshow.state',
            'label' => 'Slideshow',
            'text' => 'Enable as Slideshow Area'
        ];

        $configButton = [
            'name' => '_kicktemp_slideshow',
            'text' => 'Configuration',
            'type' => 'button-panel',
            'panel' => 'kicktemp-sectionslideshow-config',
            'enable' => 'kicktemp_slideshow.state',
            'description' => 'Enable this element as a Slideshow, and set the configuration.'
        ];

        // set button right after status field
        Arr::splice($type['fieldset']['default']['fields'][$index]['fields'], 2, 0, [$statusField, $configButton]);

        return $type;
	}
}
