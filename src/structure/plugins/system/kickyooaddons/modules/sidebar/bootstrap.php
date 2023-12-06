<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Sidebar;

use YOOtheme\Builder;
use YOOtheme\Path;
use YOOtheme\Theme\Styler\StylerConfig;

return [

	'theme' => [
		'styles' => [
			'components' => [
				'kick-sidebar' => Path::get('./assets/less/kick-sidebar.less'),
			],
		],
	],

    'config' => [
        StylerConfig::class => __DIR__ . '/config/styler.json',
    ],

	'extend' => [
		Builder::class => function (Builder $builder) {
			$builder->addTypePath(Path::get('./elements/*/element.json'));
		},
	],

];
