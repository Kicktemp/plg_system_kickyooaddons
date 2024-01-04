<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\ContactSource;

use YOOtheme\Builder;
use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Path;

return [

	'events' => [
		'source.init' => [Src\Listener\LoadSourceTypes::class => '@handle'],
		'builder.template' => [Src\Listener\MatchTemplate::class => '@handle'],
        'builder.template.load' => [Src\Listener\LoadTemplateUrl::class => '@handle'],
		BuilderConfig::class => [Src\Listener\LoadBuilderConfig::class => '@handle'],
	],
];
