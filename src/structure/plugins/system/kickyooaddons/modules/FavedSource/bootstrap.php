<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\FavedSource;

use YOOtheme\Builder;
use YOOtheme\Path;
use YOOtheme\Theme\Styler\StylerConfig;

return [
    'theme' => [
        'styles' => [
            'components' => [
                'kick-faved' => Path::get('./assets/less/kick-faved.less'),
            ],
        ],
    ],

    'config' => [
        StylerConfig::class => __DIR__ . '/config/styler.json',
    ],

    'events' => [
        'source.init' => [Src\Listener\LoadSourceTypes::class => '@handle'],
    ],

    'extend' => [
        Builder::class => function (Builder $builder) {
            $builder->addTypePath(Path::get('./elements/*/element.json'));
        },
    ],
];
