<?php

$el = $this->el('div');

// Icon
$icon = $this->el('span', [

    'uk-icon' => [
        'icon: {icon};',
        'width: {icon_width}; height: {icon_width};',
    ],

], '');

$props['option']->add_text = $props['add_text'];
$props['option']->remove_text = $props['remove_text'];
$props['favedjson'] = json_encode($props['option']);

// Link
$link = $this->el('a', [

    'class' => [
        'uk-icon-link',
        'kick-faved',
    ],

    'data-group' => ['{group}'],
    'data-articleid' => ['{articleid}'],
    'data-options' => ['{favedjson}'],
]);

?>

<?= $el($props, $attrs) ?>

    <?= $link($props, $icon($props)) ?>

<?= $el->end() ?>
