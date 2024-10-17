<?php

$el = $this->el('div');

// Layout
$grid = $this->el('div', [

    'class' => [
        'uk-grid-{gap}',
        'uk-child-width-1-1',
    ],

    'uk-grid' => true,
]);

// Input
$input = $this->el('input', [

    'class' => [
        'el-input',
        'uk-input',
        'uk-form-{form_size}',
        'uk-form-{form_style}',
    ],
]);

// Input
$textarea = $this->el('textarea', [

	'class' => [
		'el-input',
		'uk-textarea',
		'uk-form-{form_size}',
		'uk-form-{form_style}',
	],

]);

// Button
$button = $this->el('button', [

    'class' => [
        'el-button',
        'uk-button uk-button-{button_style}',
        'uk-button-{form_size} {@!button_style: text}',
        'uk-width-1-1 {@button_fullwidth} {@!layout: grid}',
    ]
]);

// Modal
$modal = $this->el('div', [
   'class' => [
	   'uk-flex-top {@modal_center}',
	   'uk-modal-container {@modal_container}',
   ],

   'id' => [
        '{modal_id}'
    ],

    'uk-modal' => true
]);

$modaldialog = $this->el('div', [
	'class' => [
        'uk-modal-dialog',
		'uk-margin-auto-vertical {@modal_center}',
	],
]);

// Close Button
$closebutton = $this->el('button', [

	'class' => [
		'uk-modal-close-{close_style}'
	],
]);

?>
<?php if ($props['show_inmodal']) : ?>
<?= $modal($props) ?>
    <?= $modaldialog($props) ?>
        <?= $closebutton($props, ['type' => 'button', 'uk-close' => true]) ?></button>
<?php if ($props['modal_header']) : ?>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title"><?= $props['modal_header'] ?></h2>
        </div>
<?php endif ?>
        <div class="uk-modal-body">
<?php endif ?>

<?= $el($props, $attrs) ?>
<form class="<?= ($props['horizontal']) ? 'uk-form-horizontal ' : ''?>uk-form uk-panel js-kickyooaddonsform-form" method="post"<?= $this->attrs($form) ?> xmlns="http://www.w3.org/1999/html"<?= ($props['novalidate']) ? ' novalidate' : '' ?><?= (isset($props['attachment']) && $props['attachment']) ? ' enctype="multipart/form-data"' : '' ?>>
        <?= $grid($props) ?>
            <?php foreach ($children as $child) : ?>
                <?= $builder->render($child, ['element' => $props]) ?>
            <?php endforeach ?>
            <?= $this
                ->el('div', ['class' => ['uk-width-auto@s {@layout: grid}']], $button($props, ['type' => 'submit'], $props['label_button'] ?: ''))
                ->render($props) ?>
            <input type="hidden" name="settings" value="<?= $settings ?>">
            <div class="message uk-margin uk-hidden"></div>
        </div>
    </form>
</div>
<?php if ($props['show_inmodal']) : ?>
    </div>
    </div>
    </div>
<?php endif ?>
