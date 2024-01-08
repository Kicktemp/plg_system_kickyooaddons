<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [AUTHOR_URL]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

defined('_JEXEC') or die;

use function YOOtheme\app;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Application;
use YOOtheme\Path;

if (!class_exists('plgSystemKickYooAddonsHelper')) {
    require_once __DIR__ . '/helper.php';
}

/**
 * KickYooAddons plugin.
 *
 * @package   plg_system_kickyooaddons
 * @since     1.0.0
 */
class plgSystemKickYooAddons extends CMSPlugin
{
	/**
	 * Application object
	 *
	 * @var    CMSApplication
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    DatabaseDriver
	 * @since  1.0.0
	 */
	protected $db;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * onAfterInitialise.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onAfterInitialise ()
	{
        try {
            plgSystemKickYooAddonsHelper::validatePlatform();
        } catch (\RuntimeException $e) {
            plgSystemKickYooAddonsHelper::adminNotice($e->getMessage());

            return;
        }

        if (!class_exists(Application::class, false)) {
            return;
        }

		Path::setAlias('~kickyooaddons', __DIR__);
		Path::setAlias('~kickyooaddons_url', Uri::root(true) . '/plugins/system/kickyooaddons');



        $modules = ['core', 'element'];

        foreach (['bannersource', 'brevo', 'colors', 'contactsource', 'favedsource', 'files', 'form', 'hubspot', 'navigator', 'sectionslideshow', 'sectionswitcher', 'sidebar'] as $addon) {
            if ($this->params->get($addon, true)) {
                $modules[] = $addon;
            }
        }

        foreach ($modules as $module) {
            app()->load('~kickyooaddons/modules/{' . $module . '{,-joomla}}/bootstrap.php');
        }
	}

	/**
	 *
	 *
	 * @since version
	 */
	public function onAjaxRegisterWebsiteYooAddons()
	{
		$app = Factory::getApplication();

		$user_token = $app->input->get('postusertoken');
		$host = Uri::getInstance()->getHost();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://kicktemp.shop/api/index.php/v1/kickpaddle/websites',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'{
    "domain": "' . $host . '",
    "state": 1
}',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'X-Joomla-Token: ' . $user_token
			),
		));

		$data     = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if (in_array($httpCode,['400', '401','403']))
		{
			$errors = json_decode($data);
			echo '<br><div class="alert alert-danger">';
			echo '<ul>';
			foreach ($errors->errors  as $error) {
				echo '<li>' . $error->title . '</li>';
			}
			echo '</ul>';
			echo '</div>';
		} else {
			echo '<br><div class="alert alert-success">' . JText::_('KICKREGISTERWEBSITE_SUCCESS') . '</div>';
		}
		$app->close();
	}

	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		if (parse_url($url, PHP_URL_HOST) == 'kicktemp.shop'
			&& strpos($url, '[ALIAS]')
			&& !strpos($url, 'domain=')) {

			$uri  = Uri::getInstance($url);

			$uri->setVar('domain', Uri::getInstance()->getHost());
			$uri->setVar('cms_version', JVERSION);
			$uri->setVar('php_version', PHP_VERSION);

			$url = $uri->toString();
		}

		return true;
	}

    public function onExtensionBeforeUpdate(string $type, ?SimpleXMLElement $manifest)
    {
        if ($manifest) {
            plgSystemKickYooAddonsHelper::preinstallThemeCheck($manifest);
        }
    }

    public function onExtensionBeforeInstall(string $method, string $type, ?SimpleXMLElement $manifest)
    {
        if ($manifest) {
            plgSystemKickYooAddonsHelper::preinstallThemeCheck($manifest);
        }
    }
}
