<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\BannerSource;

use YOOtheme\Builder\BuilderConfig;

return [

	'events' => [
		'source.init' => [Src\Listener\LoadSourceTypes::class => '@handle'],
		BuilderConfig::class => [Src\Listener\LoadBuilderConfig::class => '@handle']
	],

];
