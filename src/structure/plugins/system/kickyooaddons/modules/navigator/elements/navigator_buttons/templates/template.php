<?php
$button = $this->el('a', [
	'class' => $this->expr([
		'el-button',
		'uk-width-1-1 {@fullwidth}',
		'uk-{button_style: link-\w+}' => ['button_style' => $props['button_style']],
		'uk-button uk-button-{!button_style: |link-\w+} [uk-button-{button_size}]' => ['button_style' => $props['button_style']],
		'uk-flex-inline uk-flex-center uk-flex-middle' => $props['content'] && $props['icon'],
	], $element),
]);

$button->attr($props['type'] == 'modal' ? [
    'onclick' => ['showModal("#{modalid}")'],
] : [
	'href' => ['{link}'],
	'target' => ['_blank {@link_target}']
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
<div>
<?= $button($props) ?>

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
</div>
