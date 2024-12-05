<?php
if (!isset($props['selected']))
{
	$props['selected'] = false;
}

foreach (['form_size', 'form_style'] as $key)
{
	$props[$key] = $element[$key] ?? null;
}

if ((!isset($props['errormessage']) || $props['errormessage'] === '') && $props['required'])
{
	$props['errormessage'] = ($props['label']) ?: $props['title'];
}

if ($props['required'])
{
	if ($props['label'])
	{
		$props['label'] .= ' <sup>*</sup>';
	}

	if ($props['placeholder'] && (!$props['label'] || !$element['show_label']))
	{
		$props['placeholder'] .= ' *';
	}
}

//Grid Child
// Layout
$width = $this->el('div', [

	'class' => [

		// Breakpoint widths
		'uk-width-{width_default}',
		'uk-width-{width_small}@s',
		'uk-width-{width_medium}@m',
		'uk-width-{width_large}@l',
		'uk-width-{width_xlarge}@xl',
	],

]);

// Input
$input = $this->el('input', [

	'class' => [
		'el-input',
		'uk-input'    => $props['type'] !== 'checkbox' && $props['type'] !== 'radio',
		'uk-checkbox' => $props['type'] == 'checkbox',
		'uk-radio'    => $props['type'] == 'radio',
		'uk-form-{form_size}'    => $props['type'] !== 'checkbox' && $props['type'] !== 'radio',
		'uk-form-{form_style}'    => $props['type'] !== 'checkbox' && $props['type'] !== 'radio',
	],

	'id' => $props['title'],

	'name' => [
		'{title}'
	],

	'placeholder' => [
		'{placeholder}'
	],

	'data-message' => [
		'{errormessage}'
	],

	'data-emailmessage' => [
		'{erroremailmessage}'
	],

	'data-equal' => [
		'{equal}'
	],

	'data-equalmessage' => [
		'{errorequalmessage}'
	],

	'value' => [
		'{value}'
	],

	'required' => $props['required'],

]);

// Input
$hidden = $this->el('input', [
	'type' => 'hidden',
	'id'   => $props['title'],

	'name' => [
		'{title}'
	],

	'value' => [
		'{value}'
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

	'id' => $props['title'],

	'name' => [
		'{title}'
	],

	'placeholder' => [
		'{placeholder}'
	],

	'data-message' => [
		'{errormessage}'
	],

	'required' => (bool) $props['required'],
	'rows'     => $props['height'] ?? null

]);

// Input
$select = $this->el('select', [

	'class' => [
		'el-select',
		'uk-select',
		'uk-form-{form_size}',
		'uk-form-{form_style}',
	],

	'id' => $props['title'],

	'name' => ($props['multiple']) ? $props['title'] . '[]' : $props['title'],

	'data-message' => [
		'{errormessage}'
	],

	'placeholder' => [
		'{placeholder}'
	],

	'value'     => [
		'{value}'
	],
	'required'  => (bool) $props['required'] ?? null,
	'multiple'  => (bool) $props['multiple'] ?? null,
	'autofocus' => (bool) $props['autofocus'] ?? null,
	'size'      => $props['height'] ?? null
]);

// Icon
$inputicon = $this->el('span', [
	'class'   => [
		'uk-form-icon',
		'uk-form-icon-flip {@icon_align: right}'
	],
	'uk-icon' => [
		'icon: {icon}'
	],
]);

// Radio
$radio = $this->el('input', [

	'type' => 'radio',

	'class' => [
		'el-input',
		'uk-radio' => $props['type'] == 'radio',
		'uk-form-{form_size}',
		'uk-form-{form_style}',
	],

	'name' => $props['title'],

	'data-message' => $props['errormessage'],

	'value' => [
		'{value}'
	]

]);

$checkbox = $this->el('input', [

	'type' => 'checkbox',

	'class' => [
		'el-input',
		'uk-checkbox' => $props['type'] == 'checkboxes',
		'uk-form-{form_size}',
		'uk-form-{form_style}',
	],

	'name' => $props['title'].'[]',

	'data-message' => $props['errormessage'],

	'value' => [
		'{value}'
	]

]);
?>
<?php if ($props['type'] !== 'hidden'): ?>
	<?= $width($props) ?>
<?php endif; ?>
<?php
if ($props['label'] !== '' && $props['type'] !== 'radio' &&  $props['type'] !== 'hidden' && $props['type'] !== 'checkboxes' && ($element['show_label'] || (!$element['show_label'] && $props['mustshowlabel'])))
{
	echo '<label class="uk-form-label" for="' . $props['title'] . '">' . $props['label'] . '</label>';
}
$icon = ($props['icon']) ? $inputicon($props) . '</span>' : '';

switch ($props['type'])
{
	case 'input':

		echo $this
			->el('div', ['class' => ['uk-form-controls uk-inline uk-display-block']], $icon . $input($props, $attrs))
			->render($props);
		break;
	case 'date':
		echo $this
			->el('div', ['class' => ['uk-form-controls uk-inline uk-display-block']], $icon . $input($props, ['type' => 'date', 'min' => $props['mindate'], 'max' => $props['maxdate']]))
			->render($props);
		break;
	case 'email':
		echo $this
			->el('div', ['class' => ['uk-form-controls uk-inline uk-display-block']], $icon . $input($props, ['type' => 'email']))
			->render($props);
		break;
	case 'number':
		echo $this
			->el('div', ['class' => ['uk-form-controls uk-inline uk-display-block']], $icon . $input($props, ['type' => 'number']))
			->render($props);
		break;
	case 'textarea':
		echo '<div class="uk-form-controls uk-inline uk-display-block">';
		echo $icon;
		echo $textarea($props);
		if ($props['value'])
		{
			echo $props['value'];
		}
		echo '</textarea>';
		echo '</div>';
		break;
	case 'hidden':
		echo $hidden($props);
		break;
	case 'checkbox':
		echo '<div class="uk-form-controls uk-inline uk-display-block">';
		echo $this
			->el('label', ['class' => ['uk-text-small']], $input($props, ['type' => 'checkbox']) . ' ' . strip_tags($props['content'], '<a><br><br/><strong></strong><span>'))
			->render($props);
		echo '</div>';
		break;

	case 'text':
		echo $props['content'];
		break;
	case 'select':
		echo '<div class="uk-form-controls uk-inline uk-display-block">';
		echo $icon;
		echo $select($props);

		foreach ($children as $child)
		{
			$text     = $child->props['text'];
			$value    = $child->props['value'];
			$disabled = (bool) $child->props['disabled'];
			$selected = in_array($value, (array) $props['value']);

			if ($value == '')
			{
				$value = true;
			}

			$option = $this->el('option', compact('disabled', 'selected', 'value'), $text);

			echo $option();
		}

		echo $select->end();
		echo '</div>';
		break;
	case 'radio':
		echo '<div class="uk-form-label">' . $props['label'];
		echo '</div>';
		if ($props['radionewline'])
		{
			echo '<div class="uk-form-controls">';
		}
		else
		{
			echo '<div class="uk-grid-small uk-child-width-auto uk-grid">';
		}
		$radiocount = 0;
		foreach ($children as $child)
		{
			$text     = $child->props['text'];
			$value    = $child->props['value'];
			$disabled = (bool) $child->props['disabled'];
			$selected = in_array($value, (array) $props['value']);
			$required = ($props['required'] && $radiocount === 0) ?? false;

			if ($value == '')
			{
				$value = true;
			}
			if ($radiocount && $props['radionewline'])
			{
				echo '<br>';
			}
			echo $this
				->el('label', ['class' => ['uk-text-small']], $radio($child->props, ['checked' => $selected, 'required' => $required]) . ' ' . strip_tags($text, '<a><br><br/><strong></strong><span>'))
				->render($child->props);
			$radiocount++;
		}

		echo '</div>';
		break;
	case 'checkboxes':
		echo '<div class="uk-form-label">' . $props['label'];
		echo '</div>';
		if ($props['ckeckboxesnewline'])
		{
			echo '<div class="uk-form-controls">';
		}
		else
		{
			echo '<div class="uk-grid-small uk-child-width-auto uk-grid">';
		}
		$checkboxcount = 0;
		foreach ($children as $child)
		{
			$text     = $child->props['text'];
			$value    = $child->props['value'];
			$disabled = (bool) $child->props['disabled'];
			$selected = in_array($value, (array) $props['value']);
			$required = ($props['required'] && $checkboxcount === 0) ?? false;

			if ($value == '')
			{
				$value = true;
			}
			if ($checkboxcount && $props['ckeckboxesnewline'])
			{
				echo '<br>';
			}
			echo $this
				->el('label', ['class' => ['uk-text-small']], $checkbox($child->props, ['checked' => $selected, 'required' => $required]) . ' ' . strip_tags($text, '<a><br><br/><strong></strong><span>'))
				->render($child->props);
			$checkboxcount++;
		}

		echo '</div>';
		break;
}
?>
<?php if ($props['type'] !== 'hidden'): ?>
    </div>
<?php endif; ?>
