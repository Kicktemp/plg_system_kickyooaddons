<?php

$modal = $this->el('div', [
	'class' => [
		'el-modal',
		'uk-flex-top {@modal_center}',
		'uk-modal-container {@modal_container}',
		'uk-modal-full {@full_modifier}',
	],
    'id' => $props['modalid'],
    'uk-modal',
    //'bg-close' => 'false'
]);

$close = $this->el('button', [
	'class' => [
        'uk-modal-close-{close_style}',
	],
    'type' => 'button',
    'uk-close' => 'true'
]);

$dialog = $this->el('div', [
   'class' => [
       'uk-modal-dialog',
	   'uk-margin-auto-vertical {@modal_center}',
   ]
]);

// Grid
$grid = $this->el('div', [
	'class' => [
		'uk-flex-middle',
		$props['grid_column_gap'] == $props['grid_row_gap'] ? 'uk-grid-{grid_column_gap}' : '[uk-grid-column-{grid_column_gap}] [uk-grid-row-{grid_row_gap}]',
		'uk-child-width-1-1 {@fullwidth}',
		'uk-flex-center {@button_center}',
	],
	'uk-grid' => true,
]);

// Top Text
$toptext = $this->el('div', [

	'class' => [
		'uk-panel',
		'uk-text-{toptext_align}',
		'uk-text-{toptext_style}',
		'uk-text-{toptext_color}',
		'uk-margin[-{toptext_margin}]-bottom {@!toptext_margin: remove}',
		'uk-margin-remove-bottom {@toptext_margin: remove}',
	],

]);

// Bottom Text
$bottomtext = $this->el('div', [

	'class' => [
		'uk-panel',
        'uk-text-{bottomtext_align}',
		'uk-text-{bottomtext_style}',
		'uk-text-{bottomtext_color}',
		'uk-margin[-{bottomtext_margin}]-top {@!bottomtext_margin: remove}',
		'uk-margin-remove-top {@bottomtext_margin: remove}',
	],

]);
?>

<?= $modal($element, $attrs) ?>
    <?= $dialog($element, $attrs) ?>
        <?= $close($element, $attrs) ?>
        <?= $close->end() ?>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title"><?= $props['question'] ?></h2>
        </div>
        <div class="uk-modal-body">
<?php if ($props['toptext'] != '') : ?>
           <?php echo $toptext($props, $attrs, $props['toptext']); ?>
<?php endif; ?>
            <?= $grid($props, $attrs) ?>
	        <?php foreach ($children as $child) : ?>
		        <?= $builder->render($child, ['element' => $props, 'parentelement' => $element]) ?>
	        <?php endforeach ?>
            <?= $grid->end() ?>
<?php if ($props['bottomtext'] != '') : ?>
           <?php echo $bottomtext($props, $attrs, $props['bottomtext']); ?>
<?php endif; ?>
        </div>
        <?php if ($props['show_back']): ?>
        <div class="uk-modal-footer uk-text-right">
            <button onclick="prevModal()" data-modal="<?= $element['connect'] ?>" class="uk-button uk-button-primary"><?= $element['back_text'] ?></button>
        </div>
        <?php endif; ?>
    <?= $dialog->end() ?>
<?= $modal->end() ?>

