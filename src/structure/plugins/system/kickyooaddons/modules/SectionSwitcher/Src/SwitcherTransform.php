<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\SectionSwitcher\Src;

use YOOtheme\Builder;
use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Arr;

class SwitcherTransform
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
        if ($params['context'] !== 'render') {
            return;
        }

        if (empty($params['prefix']) || empty($params['parent'])) {
            return;
        }

        $prefix = empty($params['data-id']) ? "{$params['prefix']}#" : "{$params['data-id']}-";

        $node->attrs = $node->attrs ?? [];
        $node->attrs['data-id'] = $params['data-id'] = $prefix . $params['i'];

        if ($this->isSwitchNode($node))
        {
            $this->createSwitcher($node);
        }
    }

    protected function isSwitchNode($node): bool
    {
        return (bool) ($node->props['kicktemp_switcher']->state ?? false);
    }

    protected function createSwitcher($node)
    {
        $config             = (array) $node->props['kicktemp_switcher'];
        $switcherNode      = $this->wrap($node, $config);

        return $switcherNode;
    }

    protected function wrap($node, array $config)
    {
        $switcherNode           = $this->loadSwitcherNode($node);

        foreach ($config as $prop => $value)
        {
            $switcherNode->props[$prop] = $value;
        }

        $switcherNode->children = $node->children;

        $node->children = [$switcherNode];

        return $switcherNode;
    }

    /**
     * @param   string  $id
     *
     * @return object|void|null
     */
    public function loadSwitcherNode($node)
    {
        return $this->builder->load(json_encode([
            'type' => 'kickyooswitcher',
            'props' => array (
                "switcher_animation" => "fade",
                "switcher_height" => true,
                "nav" => "tab",
                "nav_position" => "top",
                "nav_align" => "left",
                "nav_grid_width" => "auto",
                "nav_grid_breakpoint" => "m"
            ),
            'attr' => array (
                'data-id' => $node->attrs['data-id'] . '-0-0-0',
                'id' => 'kickyooswitcher',
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

