<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Factory;

$el = $this->el('div');

// Layout
$grid = $this->el('div', [

	'class' => [
		'uk-grid-{gap}',
		'uk-child-width-1-1',
	],

	'uk-grid' => true,
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


// Hubspot
$hubspot          = $props['hubspot_guid'];
$submitButtonText = $hubspot->displayOptions->submitButtonText;

// Override Hubspot Fields
$atthebeginning = [];
$attheend = [];
$insertfields = [];
$overridefields = [];
$spamfields = [];
foreach ($children as $child)
{
	$key = $child->props['hubspot_form'];
    $insertType = $child->props['hubspot_insert'];

    if ($key === 'nohubspotfield') {
        switch ($insertType) {
            case 'atthebeginning':
                $atthebeginning[] = $child;
                break;
            case 'attheend':
                $attheend[] = $child;
                break;
            default:
                $insertfields[$insertType][] = $child;
                break;
        }
    } else {
        $overridefields[$key] = $child;
    }

    if (in_array($child->props['type'], array('honeypot', 'captcha')))
    {
	    $spamfields[] = $child;
    }
}

$children = null;

foreach ($hubspot->fieldGroups as $group)
{
	if ($props['hideRichText'] !== true)
	{
        // Todo hidden Fields
		switch ($group->richTextType)
		{
			case 'image':
			case 'text':
				if (isset($group->richText) && $group->richText !== '')
				{

					$text = new \StdClass;

					$text->type  = 'text';
					$text->props = [
                        "html_element"      => "",
						"margin"            => "default",
						"column_breakpoint" => "m",
						"column"            => null,
						"content"           => $group->richText
					];

					$text->id       = null;
					$text->attrs    = [];
					$text->children = [];


					$children[] = $text;
				}
				break;
		}
	}

	if (isset($group->fields) && count($group->fields))
	{

		foreach ($group->fields as $field)
		{
			$input                             = new \StdClass;
			$input->type                       = 'kickhubspot_input';
			$input->id                         = null;
			$input->attrs                      = [];
			$input->children                   = [];
			$input->props['title']             = $field->name;
			$input->props['label']             = $field->label;
			$input->props['required']          = $field->required;
			$input->props['multiple']          = false;
			$input->props['autofocus']         = false;
			$input->props['mustshowlabel']     = false;
			$input->props['icon']              = null;
			$input->props['errormessage']      = null;
			$input->props['errorequalmessage'] = null;
			$input->props['erroremailmessage'] = null;
			$input->props['value']             = null;
			$input->props['placeholder']       = null;

			if ($field->required && isset($props['errorrequired']))
            {
                $input->props['errormessage'] = sprintf($props['errorrequired'], $field->label);
            }

			if ($field->fieldType === 'email' && isset($props['erroremailmessage']))
			{
				$input->props['erroremailmessage'] = sprintf($props['erroremailmessage'], $field->label);
			}

			if (isset($field->description) && $field->description !== '')
			{
				$input->props['label'] = $field->label . '<br><small>' . $field->description . '</small>';
			}

			if ($props['showLabekAsPlaceholder'])
			{
				$input->props['placeholder'] = $field->label;
			}

			if (count($group->fields) == 2)
			{
				$input->props['width_default'] = $props['width_default'];
				$input->props['width_small']   = $props['width_small'];
				$input->props['width_medium']  = $props['width_medium'];
				$input->props['width_large']   = $props['width_large'];
				$input->props['width_xlarge']  = $props['width_xlarge'];
			}

			if (count($group->fields) == 3)
			{
				$input->props['width_default'] = $props['width3_default'];
				$input->props['width_small']   = $props['width3_small'];
				$input->props['width_medium']  = $props['width3_medium'];
				$input->props['width_large']   = $props['width3_large'];
				$input->props['width_xlarge']  = $props['width3_xlarge'];
			}

			switch ($field->fieldType)
			{
				case 'phone':
					$input->props['type'] = 'input';
					break;

				case 'email':
					$input->props['type'] = 'email';
					break;

				case 'dropdown':
					$input->props['type'] = 'select';

					if (!isset($field->placeholder))
					{
						$field->placeholder = $field->label;
					}
					$placeholder                    = new \StdClass;
					$placeholder->type              = 'kickselect_option';
					$placeholder->id                = null;
					$placeholder->attrs             = [];
					$placeholder->children          = [];
					$placeholder->props['text']     = $field->placeholder;
					$placeholder->props['value']    = null;
					$placeholder->props['disabled'] = false;

					$input->children[] = $placeholder;

					foreach ($field->options as $selectoption)
					{
						$option                    = new \StdClass;
						$option->type              = 'kickselect_option';
						$option->id                = null;
						$option->attrs             = [];
						$option->children          = [];
						$option->props['text']     = $selectoption->label;
						$option->props['value']    = $selectoption->value;
						$option->props['disabled'] = false;
						$input->children[]         = $option;
					}
					break;

				case 'single_line_text':
					$input->props['type'] = 'input';
					break;

				case 'multi_line_text':
					$input->props['type']   = 'textarea';
					$input->props['height'] = 5;
					break;

				case 'number':
					$input->props['type'] = 'number';
					break;

				case 'radio':
					$input->props['type']         = 'radio';
					$input->props['radionewline'] = $props['radionewline'];

					foreach ($field->options as $selectoption)
					{
						$option                    = new \StdClass;
						$option->type              = 'kickselect_option';
						$option->id                = null;
						$option->attrs             = [];
						$option->children          = [];
						$option->props['text']     = $selectoption->label;
						$option->props['value']    = $selectoption->value;
						$option->props['disabled'] = false;
						$input->children[]         = $option;
					}
					break;

				case 'multiple_checkboxes':
					$input->props['type']              = 'checkboxes';
					$input->props['ckeckboxesnewline'] = $props['ckeckboxesnewline'];

					foreach ($field->options as $selectoption)
					{
						$option                    = new \StdClass;
						$option->type              = 'kickselect_option';
						$option->id                = null;
						$option->attrs             = [];
						$option->children          = [];
						$option->props['text']     = $selectoption->label;
						$option->props['value']    = $selectoption->value;
						$option->props['disabled'] = false;
						$input->children[]         = $option;
					}
					break;

				default:
					$b = 'da';
					break;

			}

			if (in_array($field->fieldType, ['phone', 'email', 'dropdown', 'single_line_text', 'multi_line_text', 'number', 'radio', 'multiple_checkboxes']))
			{
				if (isset($overridefields[$field->name]))
				{
					if (isset($field->description) && $field->description !== '')
					{
						$field->label = $field->label . '<br><small>' . $field->description . '</small>';
					}

					$overridefields[$field->name]->props['title'] = $field->name;

                    if ($overridefields[$field->name]->props['label'] == '') {
					    $overridefields[$field->name]->props['label'] = $field->label;
                    }

                    $children[]                                   = $overridefields[$field->name];
				}
				else
				{
					$children[] = $input;
				}
			}

            if (isset($insertfields[$field->name])) {
                $children = array_merge($children, $insertfields[$field->name]);
            }
		}
	}
}

$consentTexts = [
	'communicationConsentText'      => false,
	'consentToProcessText'          => false,
	'consentToProcessCheckboxLabel' => false,
	'consentToProcessFooterText'    => false,
	'privacyText'                   => false,
];

foreach ($consentTexts as $key => $value)
{
    if (isset($hubspot->legalConsentOptions->$key) && $hubspot->legalConsentOptions->$key !== '')
    {
        $text = new \StdClass;

        $text->type  = 'text';
        $text->props = [
            "html_element"            => "",
            "margin"            => "default",
            "column_breakpoint" => "m",
            "column"            => null,
            //"content"           => '<div class="uk-text-small">' . $hubspot->legalConsentOptions->$key . '</div>'
            "content"           => $hubspot->legalConsentOptions->$key
        ];

        $text->id       = null;
        $text->attrs    = [
            'class' => ['uk-text-small']
        ];
        $text->children = [];

        $consentTexts[$key] = $text;
    }
}
$subs = false;
if (isset($hubspot->legalConsentOptions->communicationsCheckboxes)
	&& count($hubspot->legalConsentOptions->communicationsCheckboxes))
{
	$subs                             = new \StdClass;
	$subs->type                       = 'kickhubspot_input';
	$subs->id                         = null;
	$subs->attrs                      = [];
	$subs->children                   = [];
	$subs->props['title']             = 'subscription';
	$subs->props['label']             = '';
	$subs->props['required']          = false;
	$subs->props['multiple']          = null;
	$subs->props['autofocus']         = null;
	$subs->props['mustshowlabel']     = null;
	$subs->props['icon']              = null;
	$subs->props['errormessage']      = null;
	$subs->props['errorequalmessage'] = null;
	$subs->props['erroremailmessage'] = null;
	$subs->props['value']             = null;
	$subs->props['placeholder']       = null;
	$subs->props['type']              = 'checkboxes';
	$subs->props['ckeckboxesnewline'] = $props['ckeckboxesnewline'];

	foreach ($hubspot->legalConsentOptions->communicationsCheckboxes as $checkbox)
	{
		$option                    = new \StdClass;
		$option->type              = 'kickselect_option';
		$option->id                = null;
		$option->attrs             = [];
		$option->children          = [];
		$option->props['text']     = $checkbox->label;
		$option->props['value']    = $checkbox->subscriptionTypeId;
		$option->props['disabled'] = false;
		$option->props['class']    = 'uk-text-small';
		$subs->children[]          = $option;
	}
}

// Legal consent options
if (isset($hubspot->legalConsentOptions->type))
{
	$consentType = $hubspot->legalConsentOptions->type;

	switch ($consentType)
	{
		case 'explicit_consent_to_process':
		case 'implicit_consent_to_process':
			if ($consentTexts['privacyText'])
			{
				$children[] = $consentTexts['privacyText'];
			}

			if ($subs)
			{
				$children[] = $subs;
			}

			if ($consentTexts['communicationConsentText'])
			{
				$children[] = $consentTexts['communicationConsentText'];
			}
			if ($consentTexts['consentToProcessText'])
			{
				$children[] = $consentTexts['consentToProcessText'];
			}

			if ($consentTexts['consentToProcessCheckboxLabel'])
			{
				$consent                             = new \StdClass;
				$consent->type                       = 'kickhubspot_input';
				$consent->id                         = null;
				$consent->attrs                      = [];
				$consent->children                   = [];
				$consent->props['title']             = 'consentToProcess';
				$consent->props['label']             = '';
				$consent->props['content']           = $consentTexts['consentToProcessCheckboxLabel']->props['content'];
				$consent->props['required']          = true;
				$consent->props['multiple']          = null;
				$consent->props['autofocus']         = null;
				$consent->props['mustshowlabel']     = null;
				$consent->props['icon']              = null;
				$consent->props['errormessage']      = $props['errorconsent'];
				$consent->props['errorequalmessage'] = null;
				$consent->props['erroremailmessage'] = null;
				$consent->props['value']             = true;
				$consent->props['placeholder']       = null;
				$consent->props['type']              = 'checkbox';

				$children[] = $consent;
			}
			if ($consentTexts['consentToProcessFooterText'])
			{
				$children[] = $consentTexts['consentToProcessFooterText'];
			}
			break;
		//case 'implicit_consent_to_process':
		//	break;
	}
}

$children = array_merge($atthebeginning, (array) $children, $attheend, $spamfields);
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
    <form class="<?= ($props['horizontal']) ? 'uk-form-horizontal ' : '' ?>uk-form uk-panel js-kickyooaddonshubspot-form"
          method="post"<?= $this->attrs($form) ?>
          xmlns="http://www.w3.org/1999/html"<?= ($props['novalidate']) ? ' novalidate' : '' ?>>
		<?= $grid($props) ?>
		<?php foreach ($children as $child) : ?>
			<?= $builder->render($child, ['element' => $props]) ?>
		<?php endforeach ?>
<?php
if ($props['captcha'] === 'captcha' || $props['captcha'] === 'honeypotandcaptcha')
{
	$app = Factory::getApplication();
	$default = $app->get('captcha');
	$captcha = Captcha::getInstance($default, array('namespace' => 'kickhubspot'));

    if ($default !== 'recaptcha') echo '<div class="uk-form-controls uk-inline uk-display-block">';
    echo $captcha->display($props['captchatitle'], $props['captchatitle']);
    if ($default !== 'recaptcha')  echo '</div>';
}
?>
		<?= $this
			->el('div', ['class' => ['uk-width-auto@s {@layout: grid}']], $button($props, ['type' => 'submit'], $submitButtonText ?: ''))
			->render($props) ?>
<?php
if ($props['captcha'] === 'honeypot' || $props['captcha'] === 'honeypotandcaptcha')
{
    echo '<input type="hidden" id="' . $props['honeypottitle']. '" name="' . $props['honeypottitle']. '" value="">';
}
?>
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
