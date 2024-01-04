<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Form;

use YOOtheme\Builder;
use YOOtheme\Config;

return [

	'routes' => [
		[
			'post',
			'/theme/kickform/submit',
			FormApi::class . '@submit',
			['csrf' => false, 'allowed' => true]
		],
	],

	'events' => [
		'source.init' => [Src\Listener\LoadSourceTypes::class => '@handle'],
		'customizer.init' => [Src\Listener\LoadCustomizerData::class => ['@handle', 10]],
	],

	'extend' => [
		Builder::class => function (Builder $builder) {
			$builder->addTypePath(__DIR__ . '/elements/*/element.json');
		},
	],

	'services' => [
		Src\FormApi::class => function (Config $config) {
			return new Src\FormApi(
				$config('app.secret')
			);
		},
	],

];
