<?php
/**
 * @package    plg_system_yooparcelly
 *
 * @author     Kicktemp GmbH <hello@kicktemp.com>
 * @copyright  Copyright © 2021 Kicktemp GmbH. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://kicktemp.com
 */

namespace Kicktemp\Yootheme\Navigator;

use YOOtheme\Builder;
use YOOtheme\Config;
use YOOtheme\HttpClientInterface;
use YOOtheme\Path;
use YOOtheme\Translator;

return [

	'events' => [
		'customizer.init' => [Listener\LoadCustomizerData::class => ['@handle', 10]],
	],

	'extend' => [

		Builder::class => function (Builder $builder) {
			$builder->addTypePath(Path::get('./elements/*/element.json'));
		},

	],

];
