<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */


namespace Kicktemp\YOOaddons\Navigator;

use YOOtheme\Builder;
use YOOtheme\Path;

return [
	'events' => [
		'customizer.init' => [Src\Listener\LoadCustomizerData::class => ['@handle', 10]],
	],

	'extend' => [
		Builder::class => function (Builder $builder) {
			$builder->addTypePath(Path::get('./elements/*/element.json'));
		},
	],
];
