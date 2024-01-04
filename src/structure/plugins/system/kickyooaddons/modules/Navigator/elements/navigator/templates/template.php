<?php
$props['connect'] = "js-{$this->uid()}";

$el = $this->el('div', [

	'class' => [
		'el-item',
		'kicknavigator',
	],

	'data-modal' => ['{connect}']
]);

$button = $this->el('a', [

	'class' => [
		'uk-width-1-1 {@button_fullwidth}',
		'uk-{button_style: link-\w+}' => ['button_style' => $props['button_style']],
		'uk-button uk-button-{!button_style: |link-\w+} [uk-button-{button_size}]' => ['button_style' => $props['button_style']],
	],

	'title' => ['{link_title}'],

	'href' => ['#{modalid}'],

	'uk-toggle' => true,
]);

?>


<?= $el($props, $attrs) ?>
<?php if ($props['modalid'] != '') : ?>
<?= $button($props) ?>
<?php if ($props['icon']) : ?>
	<?php if ($props['icon_align'] == 'left') : ?>
        <span uk-icon="<?= $props['icon'] ?>"></span>
	<?php endif ?>
    <span class="uk-text-middle"><?= $props['button_text'] ?></span>
	<?php if ($props['icon_align'] == 'right') : ?>
        <span uk-icon="<?= $props['icon'] ?>"></span>
	<?php endif ?>
<?php else : ?>
	<?= $props['button_text'] ?>
<?php endif ?>
</a>
<?php endif ?>
    <?php foreach ($children as $child) : ?>
    <?= $builder->render($child, ['element' => $props]) ?>
    <?php endforeach ?>
<?= $el->end() ?>
