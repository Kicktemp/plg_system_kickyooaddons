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

			$metadata->set('script:kickyoopopupimage-popupimage', ['src' => '~kickyooaddons_url/modules/PopupImage/elements/popupimage/app/popupimage.min.js', 'defer' => false]);

            return (bool) $node->props['image'];
		}

	],

];
