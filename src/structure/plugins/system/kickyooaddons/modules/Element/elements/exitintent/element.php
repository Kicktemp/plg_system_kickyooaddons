<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

use function YOOtheme\App;
use YOOtheme\Metadata;

return [

	'transforms' => [

		'render' => function ($node) {

			/** @var Metadata $metadata */
			$metadata = app(Metadata::class);

			$metadata->set('script:kickyooexitintent-exitintent', ['src' => '~kickyooaddons_url/modules/Element/elements/exitintent/app/exitintent.min.js', 'defer' => false]);
		}

	],

];
