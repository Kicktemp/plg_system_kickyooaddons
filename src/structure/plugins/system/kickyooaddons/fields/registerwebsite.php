<?php
/**
 * @package    [PROJECT_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class JFormFieldRegisterwebsite extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'registerwebsite';

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JForm  $form  The form to attach to the form field object.
	 *
	 * @since   11.1
	 */
	public function __construct($form = null)
	{
		parent::__construct();
		HTMLHelper::_('jquery.framework');
		$doc = Factory::getDocument();
		$js  = array();

		$js[] = '(function ($) {';
		$js[] = '$(document).ready(function() {';
		$js[] = "$('#registerwebsiteajax').click(function(){
					var usertoken = jQuery('#usertoken').val();
					$.ajax({
						type: 'POST',
						url: '" . JUri::base(false) . "index.php?option=com_ajax&plugin=RegisterWebsiteYooAddons&group=system&format=raw',
						data: { postusertoken: usertoken}
					})
					.done(function( data ) {
					    $('#registerstate').html(data);
					});
				});
		";
		$js[] = '});';
		$js[] = '}(jQuery));';

		$js = implode("\n", $js);
		$doc->addScriptDeclaration($js);
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput()
	{
		$html   = array();
		$html[] = '<input type="text" class="form-control span12" placeholder="' . JText::_('KICKREGISTERWEBSITE_TOKEN') . '" id="usertoken">';
		$html[] = '<br><input type="button" class="btn btn-success" value="' . JText::_('KICKREGISTERWEBSITE_REGISTER') . '" id="registerwebsiteajax">';
		$html[] = '<div id="registerstate"></div>';

		return implode("\n", $html);
	}

}
