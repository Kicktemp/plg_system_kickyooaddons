<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\SectionSwitcher;

use Kicktemp\YOOaddons\SectionSwitcher\Src\SwitcherTransform;
use Kicktemp\YOOaddons\SectionSwitcher\Src\Listener\LoadCustomizerData;
use Kicktemp\YOOaddons\SectionSwitcher\Src\Listener\FormListener;
use YOOtheme\Builder;
use YOOtheme\Config;
use YOOtheme\HttpClientInterface;
use YOOtheme\Path;
use YOOtheme\Translator;

return [

	'events' => [
		'customizer.init' => [LoadCustomizerData::class => ['@handle', 10]],
        'builder.type' => [FormListener::class => ['addFormPanel', -10]]
	],

	'extend' => [

		Builder::class => function (Builder $builder) {
			$builder->addTypePath(Path::get('./elements/*/element.json'));
            $builder->addTransform('render', new SwitcherTransform($builder));
		},

	],
];
