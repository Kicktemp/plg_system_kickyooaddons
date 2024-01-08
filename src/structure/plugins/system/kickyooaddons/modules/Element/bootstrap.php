<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [AUTHOR_URL]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Element;

use YOOtheme\Builder;
use YOOtheme\Path;

return [
    'extend' => [
        Builder::class => function (Builder $builder) {
            $builder->addTypePath(Path::get('./elements/*/element.json'));
        },
    ],
];
