<?php

$el = $this->el('div', [

    'uk-slideshow' => $this->expr([
        'ratio: false;',
        'animation: {slideshow_animation};',
        'velocity: {slideshow_velocity};',
        'autoplay: {slideshow_autoplay}; [pauseOnHover: false; {!slideshow_autoplay_pause}; ] [autoplayInterval: {slideshow_autoplay_interval}000;]',
    ], $props) ?: true,

]);

// Container
$container = $this->el('div', [

    'class' => [
        'uk-position-relative',
        'uk-visible-toggle {@slidenav} {@slidenav_hover}',
    ],

    'tabindex' => ['-1 {@slidenav} {@slidenav_hover}'],

]);

// Items
$items = $this->el('ul', [

    'class' => [
        'uk-slideshow-items',
        'uk-box-shadow-{slideshow_box_shadow}',
    ],

    'uk-height-viewport' => $props['slideshow_height'] ? [
        'offset-top: true;',
        'offset-bottom: 20; {@slideshow_height: percent}',
        'offset-bottom: !.uk-section +; {@slideshow_height: section}',
    ] : false,

]);
$attrs['id'] = $attr->id;
?>

<?= $el($props, $attrs) ?>

    <?= $container($props) ?>


            <?= $items($props) ?>
                <?php foreach ($children as $i => $child) : ?>
                <li class="el-item">
                    <div class="getheight">
                    <?= $builder->render($child, ['i' => $i, 'element' => $props]) ?>
                    </div>
                </li>
                <?php endforeach ?>
            </ul>


        <?php if ($props['slidenav']) : ?>
        <?= $this->render("{$__dir}/template-slidenav") ?>
        <?php endif ?>

        <?php if ($props['nav'] && !$props['nav_below']) : ?>
        <?= $this->render("{$__dir}/template-nav") ?>
        <?php endif ?>

    </div>

    <?php if ($props['nav'] && $props['nav_below']): ?>
    <?= $this->render("{$__dir}/template-nav") ?>
    <?php endif ?>

</div>
