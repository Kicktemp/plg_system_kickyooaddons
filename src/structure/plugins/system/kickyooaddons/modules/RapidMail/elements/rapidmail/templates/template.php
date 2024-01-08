<?php

$props['id'] = "js-{$this->uid()}";

$el = $this->el('div');

// Panel/Card
$panel = $this->el('div', [

	'class' => [
		'uk-panel {@!panel_style}',
		'uk-card uk-{panel_style} [uk-card-{panel_size}]',
		'uk-card-hover {@!panel_style: |card-hover} {@panel_link} {@link}',
		'uk-card-body {@panel_style}',
	],

]);

// Content
$content = $this->el('div', [

	'class' => [
		'uk-card-body uk-margin-remove-first-child {@panel_style} {@has_panel_card_image}',
		'uk-padding[-{!panel_content_padding: |default}] uk-margin-remove-first-child {@!panel_style} {@has_panel_content_padding}',
	],

]);

// Layout
$grid = $this->el('div', [

    'class' => [
        'uk-grid-{gap}',
        'uk-child-width-expand@s {@layout: grid}',
        'uk-child-width-1-1 {@layout: stacked(-name)?}',
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

// Button
$buttongrid = $this->el('div', [
	'class' => ['uk-width-auto@s {@layout: grid}']
]);

$button = $this->el('button', [

    'class' => [
        'el-button',
        'uk-button uk-button-{button_style}',
        'uk-button-{form_size} {@!button_style: text}',
        'uk-width-1-1 {@button_fullwidth} {@!layout: grid}',
        'uk-margin[-small{@!button_margin: default}]-{0} {@show_name} {@button_margin}' => $props['layout'] == 'grid' ? 'left' : 'top',
        'uk-flex-inline uk-flex-center uk-flex-middle' => $props['label_button'] && $props['button_icon'],
    ],

    'title' => ['{label_button}'],

]);

$modal = $this->el('div', [
	'class' => [
		'uk-flex-top {@modal_center}',
		'uk-modal-container {@modal_container}',
	],

	'id' => [
		'{id}'
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

// Icon
$icon = $this->el('span', [

	'class' => [
		'uk-margin-small-right' => $props['label_button'] && $props['button_icon_align'] == 'left',
		'uk-margin-small-left' => $props['label_button'] && $props['button_icon_align'] == 'right',
	],
	'uk-icon' => $props['button_icon'],

]);


// MODAL BUTTON
$modalbutton = $this->el('button', [

	'class' => [
		'el-button',
		'uk-button uk-button-{modal_style}',
		'uk-button-{modal_form_size} {@!modal_style: text}',
		'uk-width-1-1 {@modal_fullwidth} {@!layout: grid}',
		'uk-flex-inline uk-flex-center uk-flex-middle' => $props['modal_buttoncontent'] && $props['modal_icon'],
	],

	'title' => ['{modal_buttoncontent}'],

]);

if ($props['show_inmodal']) {
	$modalbutton->attr([
		'uk-toggle' => ['target: #{id}']
    ]);
}

// Icon
$modalicon = $this->el('span', [

	'class' => [
		'uk-margin-small-right' => $props['modal_buttoncontent'] && $props['modal_icon_align'] == 'left',
		'uk-margin-small-left' => $props['modal_buttoncontent'] && $props['modal_icon_align'] == 'right',
	],
	'uk-icon' => $props['modal_icon'],

]);

?>
<?php if ($props['show_inmodal']) : ?>
<?= $modalbutton($props) ?>

<?php if ($props['modal_icon'] && $props['modal_icon_align'] == 'left') : ?>
	<?= $modalicon($props, '') ?>
<?php endif ?>

<?php if ($props['modal_buttoncontent']) : ?>
	<?= $props['modal_buttoncontent'] ?>
<?php endif ?>

<?php if ($props['modal_icon'] && $props['modal_icon_align'] == 'right') : ?>
	<?= $modalicon($props, '') ?>
<?php endif ?>
    </button>
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

<?= $panel($props, $attrs) ?>
<?= $el($props, $attrs) ?>
	<?php if ($this->expr($content->attrs['class'], $props)) : ?>
		<?= $content($props, $this->render("{$__dir}/template-content", $props)) ?>
	<?php else : ?>
		<?= $this->render("{$__dir}/template-content",$props) ?>
	<?php endif ?>

    <form class="uk-form uk-panel js-form-rapidmail" method="post"<?= $this->attrs($form) ?><?= ($props['novalidate']) ? ' novalidate' : '' ?>>

        <?= $grid($props) ?>

            <?php if ($props['show_name']) : ?>

                <?php if ($props['layout'] == 'stacked-name') : ?>
                <div>
                    <div class="uk-child-width-1-2@s <?= $props['gap'] ? "uk-grid-{$props['gap']}" : '' ?>" uk-grid>
                <?php endif ?>

                <?php if ($props['show_first_name']) : ?>
                    <div class="uk-form-controls uk-inline uk-display-block"><?= $input($props, ['name' => 'firstname', 'placeholder' => ['{label_first_name}'], 'data-message' => $props['required']->firstname, 'required' => true]) ?></div>
                <?php endif ?>
                <?php if ($props['show_last_name']) : ?>
                    <div class="uk-form-controls uk-inline uk-display-block"><?= $input($props, ['name' => 'lastname', 'placeholder' => ['{label_last_name}'], 'data-message' => $props['required']->lastname,'required' => true]) ?></div>
                <?php endif ?>

                <?php if ($props['layout'] == 'stacked-name') : ?>
                    </div>
                </div>
                <?php endif ?>

            <?php endif ?>

        <div class="uk-form-controls uk-inline uk-display-block"><?= $input($props, ['type' => 'email', 'name' => 'email', 'placeholder' => ['{label_email}'],'data-message' => $props['required']->email, 'data-emailmessage' => $props['required']->wrongemail, 'required' => true]) ?></div>

	    <?php if ($props['privacytext']) : ?>
        <style>
            .privacytext > *:nth-child(2) {
                display: inline;
            }
        </style>
        <div>
            <label class="privacytext uk-text-small"><input class="uk-checkbox" type="checkbox" name="privacy" data-message="<?= $props['required']->privacy ?>" required> <?= strip_tags($props['privacytext'], '<a><br><br /><span>')?></label>
        </div>
	    <?php endif ?>

	    <?= $buttongrid($props) ?>
            <?= $button($props) ?>

            <?php if ($props['button_icon'] && $props['button_icon_align'] == 'left') : ?>
                <?= $icon($props, '') ?>
            <?php endif ?>

            <?php if ($props['label_button']) : ?>
                <?= $props['label_button'] ?>
            <?php endif ?>

            <?php if ($props['button_icon'] && $props['button_icon_align'] == 'right') : ?>
                <?= $icon($props, '') ?>
            <?php endif ?>

	        <?= $button->end() ?>
        <?= $buttongrid->end() ?>
        <?= $grid->end() ?>
        <input type="hidden" name="settings" value="<?= $settings ?>">
        <div class="message uk-margin uk-hidden"></div>
    </form>
<?= $el->end() ?>
<?= $panel->end() ?>
<?php if ($props['show_inmodal']) : ?>
    </div>
<?= $modaldialog->end() ?>
<?= $modal->end() ?>
<?php endif ?>
