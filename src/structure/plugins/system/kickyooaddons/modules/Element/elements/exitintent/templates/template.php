<?php
// Button
$button = $this->el('a', [

	'class' => [
		'el-content',
		'uk-width-1-1 {@fullwidth}',
		'uk-{button_style: link-\w+}' => ['button_style' => $props['button_style']],
		'uk-button uk-button-{!button_style: |link-\w+} [uk-button-{button_size}]' => ['button_style' => $props['button_style']],
	],

	'title' => ['{link_title}'],
]);

$button->attr($props['link_target'] == 'modal' ? [
	'href' => ['#{id}'],
	'uk-toggle' => true,
] : [
	'href' => ['{link}'],
	'target' => ['_blank {@link_target}'],
	'uk-scroll' => str_contains((string) $props['link'], '#'),
]);

$body_container = $this->el('div', [
	'class' => [
        'uk-modal-body',
		'uk-background-{style}',
		'uk-text-{text_align}',
        // Text color
        'uk-{text_color}',
	],
]);

$head_container = $this->el('div', [
	'class' => [
		'uk-modal-header',
		'uk-background-{style}',
		'uk-text-{text_align}',
		// Text color
		'uk-{text_color}',
	],
]);
?>
<div id="exit_<?= $props['id'] ?>" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-outside" type="button" uk-close></button>
	    <?= $head_container($props) ?>
            <h2 class="uk-modal-title uk-text-center"><?= $props['title'] ?></h2>
        </div>
	    <?= $body_container($props) ?>
            <?= $props['content'] ?>
            <?php if ($props['link'] !== '' && $props['link_title'] !== ''): ?>
            <?= $button($props) ?>
                <?php if ($props['icon']) : ?>

                <?php if ($props['icon_align'] == 'left') : ?>
                <span uk-icon="<?= $props['icon'] ?>"></span>
                <?php endif ?>

                <span class="uk-text-middle"><?= $props['button_content'] ?></span>

                <?php if ($props['icon_align'] == 'right') : ?>
                <span uk-icon="<?= $props['icon'] ?>"></span>
                <?php endif ?>

                <?php else : ?>
                <?= $props['button_content'] ?>
                <?php endif ?>

            </a>
           <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
   exitIntent.init({modalid:'exit_<?= $props['id'] ?>',cookieExp:<?= (int) $props['exitintent']?>,delay:<?= (int) $props['exitintent_delay']?>,showOncePerSession:<?= ($props['exitintent_session'])? 1:0 ?>,showOnDelay:<?= ($props['show_on_delay'])? 1:0 ?>})
</script>

