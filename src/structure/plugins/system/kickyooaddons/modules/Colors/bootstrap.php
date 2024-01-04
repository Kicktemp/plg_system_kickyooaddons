<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Colors;

use YOOtheme\Path;
use YOOtheme\Theme\Styler\StylerConfig;

return [

	'theme' => [
		'styles' => [
			'components' => [
				'kickvariables' => Path::get('./assets/less/variables.less'),
				'kickinverse' => Path::get('./assets/less/inverse.less'),
				'kickbutton' => Path::get('./assets/less/button.less'),
				'kickcard' => Path::get('./assets/less/card.less'),
				'kicksection' => Path::get('./assets/less/section.less'),
				'kicktext' => Path::get('./assets/less/text.less'),
				'kicktile' => Path::get('./assets/less/tile.less'),
			],
		],
	],

	'config' => [
		StylerConfig::class => __DIR__ . '/config/styler.json',
	],

	'events' => [
		'builder.type' => [
			Src\Listener\FormListener::class => ['addStyleOptions', -10]
		]
	],
];
