<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\SectionSlideshow\Src;

use YOOtheme\Builder;
use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Arr;

class SlideshowTransform
{
    /** @var Builder */
    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Transform callback.
     *
     * @param   object  $node
     * @param   array   $params
     */
    public function __invoke($node, array $params)
    {
        if ($this->isFormNode($node))
        {
            $this->createForm($node);
        }
    }

    protected function isFormNode($node): bool
    {
        return (bool) ($node->props['kicktemp_slideshow']->state ?? false);
    }

    protected function createForm($node)
    {
        $config             = (array) $node->props['kicktemp_slideshow'];
        $slideshowNode      = $this->wrap($node, $config);

        return $slideshowNode;
    }

    protected function wrap($node, array $config)
    {
        $slideshowNode           = $this->loadSlideshowNode($node);
        foreach ($config as $prop => $value)
        {
            $slideshowNode->props[$prop] = $value;
        }

        $slideshowNode->children = $node->children;

        $node->children = [$slideshowNode];

        return $slideshowNode;
    }

    /**
     * @param   string  $id
     *
     * @return object|void|null
     */
    public function loadSlideshowNode($node)
    {
        return $this->builder->load(json_encode([
            'type' => 'kickyooslide',
            'props' => array (
                'show_title' => true,
                'show_meta' => true,
                'show_content' => true,
                'show_link' => true,
                'show_thumbnail' => true,
                'slideshow_min_height' => 300,
                'slideshow_autoplay_pause' => true,
                'nav' => 'dotnav',
                'nav_position' => 'bottom-center',
                'nav_position_margin' => 'medium',
                'nav_align' => 'center',
                'nav_breakpoint' => 's',
                'thumbnav_width' => '100',
                'thumbnav_height' => '75',
                'thumbnav_svg_color' => 'emphasis',
                'slidenav' => 'default',
                'slidenav_margin' => 'medium',
                'slidenav_breakpoint' => 's',
                'slidenav_outside_breakpoint' => 'xl',
                'overlay_position' => 'center-left',
                'overlay_animation' => 'parallax',
                'title_element' => 'h3',
                'meta_style' => 'meta',
                'meta_align' => 'below-title',
                'meta_element' => 'div',
                'link_text' => 'Read more',
                'link_style' => 'default',
                'margin' => 'default'
            ),
            'attr' => array (
                'data-id' => $node->attrs['data-id'] . '-0-0-0',
                'id' => 'kickyooslide',
                'class' =>
                    array (
                        'uk-visible@{0}' => '',
                        'uk-hidden@{0}' => '',
                        0 => 'uk-margin {@margin: default}',
                        1 => 'uk-margin-{!margin: |default}',
                        2 => 'uk-margin-remove-top {@margin_remove_top}',
                        3 => 'uk-margin-remove-bottom {@margin_remove_bottom}',
                    ),
            ),
            'parent' => 'false',
        ]), ['context' => 'render']);
    }
}

