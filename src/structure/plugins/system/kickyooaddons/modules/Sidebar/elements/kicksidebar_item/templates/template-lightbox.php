<?php

if ($props['link_target'] != 'modal') {
    return;
}

$link = $this->el('image', [
    'src' => $props['link'],
    'width' => $props['lightbox_width'],
    'height' => $props['lightbox_height'],
]);

// Modal
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

if ($this->isImage($props['link'])) {

    $lightbox = $link($element, ['thumbnail' => true]);

} elseif ((bool) strlen($props['modalcontent'])) {
	$el = $this->el('div', [

		'class' => [
			'uk-paasdl',
		],

	]);
  $lightbox = $el($props, $attrs, $props['modalcontent']);
} else {

    $video = $this->isVideo($props['link']);
    $iframe = $this->iframeVideo($props['link'], [], false);
    $lightbox = $video && !$iframe ? $link($element, [

        // Video
        'controls' => true,
        'uk-video' => true,

    ], '', 'video') : $link($element, [

        // Iframe
        'src' => $iframe ?: $props['link'],
        'frameborder' => 0,
        'uk-video' => $video || $iframe,
        'allowfullscreen' => true,
        'uk-responsive' => true,

    ], '', 'iframe');

}

?>

<?php // uk-flex-top is needed to make vertical margin work for IE11 ?>
<?= $modal($props) ?>
<?= $modaldialog($props) ?>
<?= $closebutton($props, ['type' => 'button', 'uk-close' => true]) ?><?= $closebutton->end() ?>
<?php if ($props['modal_header']) : ?>
    <div class="uk-modal-header">
        <h2 class="uk-modal-title"><?= $props['modal_header'] ?></h2>
    </div>
<?php endif ?>
<div class="uk-modal-body">
    <?= $lightbox ?>
</div>
<?= $modaldialog->end() ?>
<?= $modal->end() ?>
