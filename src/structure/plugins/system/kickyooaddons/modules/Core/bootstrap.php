<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Core;

return [
	'events' => [
		'customizer.init' => [Src\Listener\LoadCustomizerData::class => ['@handle', 10]],
	],
];
