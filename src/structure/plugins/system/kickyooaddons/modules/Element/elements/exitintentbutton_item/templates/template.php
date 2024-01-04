<?php

$props['id'] = "js-{$this->uid()}";

// Button
$button = $this->el('a', [

    'class' => $this->expr([
        'el-content',
        'uk-width-1-1 {@fullwidth}',
        'uk-{button_style: link-\w+}' => ['button_style' => $props['button_style']],
        'uk-button uk-button-{!button_style: |link-\w+} [uk-button-{button_size}]' => ['button_style' => $props['button_style']],
    ], $element),

    'title' => ['{link_title}'],

    'uk-toggle' => [
            'target: #{toggle}'
    ],

    'data-modalid' => ['#{toggle}']

]);

$button->attr($props['link_target'] == 'modal' ? [
    'href' => ['#{id}'],
    'uk-toggle' => true,
] : [
    'href' => ['{link}'],
    'target' => ['_blank {@link_target}'],
    'uk-scroll' => strpos($props['link'], '#') === 0,
]);

$inputvalues = [];
if ($props['toggle'] && $props['toggle'] !== '' && count($children))
{
    $input = 'UIkit.util.$("#' .$props['toggle'] .' [name=%s]").value="%s";' ;
    $textarea = 'UIkit.util.$("#' .$props['toggle'] .' [name=%s]").innterText="%s";' ;
    $checkbox = 'UIkit.util.$("#' .$props['toggle'] .' [name=%s]").checked;' ;

    foreach ($children as $child)
    {
        $name = $child->props['text'];
        $type = $child->props['type'];
        $value = addslashes($child->props['value']);

        switch ($type)
        {
            case 'textarea':
	            $inputvalues[] = sprintf('UIkit.util.$("#' .$props['toggle'] .' [name=%s]").innerText="%s"', $name, $value);
                break;
            case 'checkbox':
	            $inputvalues[] = sprintf('UIkit.util.$("#' .$props['toggle'] .' [name=%s]").checked = true', $name, $value);
                break;
            default:
	            $inputvalues[] = sprintf('UIkit.util.$("#' .$props['toggle'] .' [name=%s]").value="%s"', $name, $value);
                break;
        }
    }

	if (count($inputvalues))
	{
		$button->attr([
			'onclick' => implode(';', $inputvalues)
		]);
	}
}
?>


<?= $button($props) ?>

<?php if ($props['icon']) : ?>

    <?php if ($props['icon_align'] == 'left') : ?>
    <span uk-icon="<?= $props['icon'] ?>"></span>
    <?php endif ?>

    <span class="uk-text-middle"><?= $props['content'] ?></span>

    <?php if ($props['icon_align'] == 'right') : ?>
    <span uk-icon="<?= $props['icon'] ?>"></span>
    <?php endif ?>

<?php else : ?>
<?= $props['content'] ?>
<?php endif ?>

</a>

<?= $this->render("{$__dir}/template-lightbox", compact('props')) ?>
