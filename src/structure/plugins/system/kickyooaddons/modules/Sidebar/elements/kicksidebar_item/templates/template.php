<?php

$props['id'] = "js-{$this->uid()}";

// Item
$item = $this->el('a', [

    'class' => [
        'kick-sidebar-item',
	    'kick-sidebar-item-{style}',
        'uk-flex-inline uk-flex-center uk-flex-middle' => $props['content'] && $props['icon'],
    ],

    'title' => ['{link_title}'],

    'rel' => [
        'noopener noreferrer' => $props['noopener']
    ],

]);

$item->attr($props['link_target'] == 'modal' ? [
    'href' => ['#{id}'],
    'uk-toggle' => true,
] : [
    'href' => ['{link}'],
    'target' => ['_blank {@link_target}'],
    'uk-scroll' => (str_starts_with((string) $props['link'], '#') && !$props['uk_toggle']),
    'uk-toggle' => (str_starts_with((string) $props['link'], '#')  && $props['uk_toggle']),
]);

// Icon
$icon = $this->el('span', [

    'class' => [
        'uk-margin-small-right' => $props['content'] && $props['icon_align'] == 'left',
        'uk-margin-small-left' => $props['content'] && $props['icon_align'] == 'right',
    ],
    'uk-icon' => $props['icon'],

]);

?>

<?= $item($props) ?>

    <?php if ($props['icon'] && $props['icon_align'] == 'left') : ?>
    <?= $icon($props, '') ?>
    <?php endif ?>

    <?php if ($props['content']) : ?>
    <?= $props['content'] ?>
    <?php endif ?>

    <?php if ($props['icon'] && $props['icon_align'] == 'right') : ?>
    <?= $icon($props, '') ?>
    <?php endif ?>

</a>

<?= $this->render("{$__dir}/template-lightbox", compact('props')) ?>
