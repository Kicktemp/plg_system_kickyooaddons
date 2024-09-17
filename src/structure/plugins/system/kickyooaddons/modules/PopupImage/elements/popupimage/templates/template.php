<?php
$js_id = $this->uid();

// Image
$image = $this->el('image', [
    'class' => [
        'el-popupimage'
    ],

    'src' => $props['image'],
    'alt' => $props['image_alt'],
    'loading' => $props['image_loading'] ? false : null,
    'width' => $props['image_width'],
    'height' => $props['image_height'],
    'focal_point' => $props['image_focal_point'],
    'thumbnail' => true,
]);

// Link and Lightbox
$link = $this->el('a', [

    'class' => [
        'el-link',
    ],

    'href' => ['{link}'],
    'aria-label' => ['{link_aria_label}'],
    'target' => ['_blank {@link_target: blank}'],
    'uk-scroll' => str_contains((string) $props['link'], '#'),
]);


// Close Button
$closebutton = $this->el('button', [

    'class' => [
        'kick-popupimage-close',
        'uk-modal-close-{close_style}'
    ],
]);
?>

<div id="popupimage_<?= $js_id ?>" class="kick-popupimage uk-hidden">
    <?= $closebutton($props, ['type' => 'button', 'uk-close' => true]) ?></button>
    <?php if ($props['link']) : ?>
    <?= $link($props, $image($props)) ?>
    <?php else : ?>
        <?= $image($props) ?>
    <?php endif ?>
</div>
<script type="text/javascript">
   popupimage.init({modalid:'popupimage_<?= $js_id ?>',cookieExp:<?= (int) $props['popupimage']?>,delay:<?= (int) $props['popupimage_delay']?>,showOncePerSession:<?= ($props['popupimage_session'])? 1:0 ?>,showOnDelay:<?= ($props['show_on_delay'])? 1:0 ?>})
</script>

