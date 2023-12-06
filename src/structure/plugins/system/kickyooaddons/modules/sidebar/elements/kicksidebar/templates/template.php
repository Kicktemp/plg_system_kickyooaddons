<?php

$el = $this->el('div', [

    'class' => [
      'kick-sidebar'
    ],
]);

?>

<?= $el($props, $attrs) ?>

    <?php foreach ($children as $child) : ?>

        <?= $builder->render($child, ['element' => $props]) ?>

    <?php endforeach ?>

</div>
