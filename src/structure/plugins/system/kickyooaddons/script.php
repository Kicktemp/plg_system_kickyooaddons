<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;

/**
 * [PACKAGE_NAME] script file.
 *
 * @package   [PACKAGE_NAME]
 * @since     1.0.0
 */
class plgSystemKickYooAddonsInstallerScript extends InstallerScript
{
	const MIN_YTP_VERSION = '4.0.0-beta.11';
	const MAX_YTP_VERSION = '4.0.99';
	const MIN_YTP_VERSION_TXT = 'YOOtheme Pro v%s is not supported by [PROJECT_NAME] for YOOtheme Pro plugin. Update to YOOtheme Pro v%s or higher or disable the [PROJECT_NAME] plugin.';
	const MAX_YTP_VERSION_TXT = 'YOOtheme Pro v%s is not supported by the currently installed version of [PROJECT_NAME] for YOOtheme Pro plugin. Update [PROJECT_NAME] to its latest version before retrying.';
	protected $minimumPhp = '7.2.5';
	protected $minimumJoomla = '4.0';
	protected $deleteFiles = ['/plugins/system/kickyooaddons/src/SettingsListener.php'];


	public function preflight($type, $parent)
	{
		if (!in_array($type, ['install', 'update']))
		{
			return true;
		}

		$app = Factory::getApplication();

		try
		{
			self::checkYootheme();
		}
		catch (\RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'warning');

			return false;
		}
	}

	private static function checkYootheme()
	{
		$theme = simplexml_load_file(JPATH_ROOT . '/templates/yootheme/templateDetails.xml');

		if ($theme === false)
		{
			throw new \RuntimeException('[PROJECT_NAME] for YOOtheme Pro requires YOOtheme Pro theme to be installed and active.');
		}

		$version = (string) $theme->version;

		if (version_compare($version, self::MIN_YTP_VERSION, 'lt'))
		{
			throw new \RuntimeException(
				sprintf(self::MIN_YTP_VERSION_TXT, $version, self::MIN_YTP_VERSION)
			);
		}

		if (version_compare($version, self::MAX_YTP_VERSION, 'gt'))
		{
			throw new \RuntimeException(
				sprintf(self::MAX_YTP_VERSION_TXT, $version)
			);
		}
	}

	/**
	 * @return void
	 */
	public function install()
	{
		Factory::getDbo()->setQuery("UPDATE #__extensions SET enabled = 1 WHERE type = 'plugin' AND folder = 'system' AND element = 'kickyooaddons'")->execute();
	}

	/**
	 * Function to perform changes during postflight
	 *
	 * @param   string            $type    The action being performed
	 * @param   ComponentAdapter  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   4.0.0v1
	 */

	public function postflight($install_type, $parent)
	{
		if (!in_array($install_type, ['install', 'update']))
		{
			return true;
		}

		$this->removeFiles();
	}
}
