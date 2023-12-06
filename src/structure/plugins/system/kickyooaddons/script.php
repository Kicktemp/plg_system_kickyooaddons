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

if (!class_exists('plgSystemKickYooAddonsHelper')) {
    require_once __DIR__ . '/helper.php';
}

/**
 * [PACKAGE_NAME] script file.
 *
 * @package   [PACKAGE_NAME]
 * @since     1.0.0
 */
class plgSystemKickYooAddonsInstallerScript extends InstallerScript
{
	protected $minimumPhp = '7.2.5';
	protected $minimumJoomla = '4.0';
	protected $deleteFiles = ['/plugins/system/kickyooaddons/src/SettingsListener.php'];


	public function preflight($type, $parent)
	{
		if (!in_array($type, ['install', 'update']))
		{
			return true;
		}

        try {
            plgSystemKickYooAddonsHelper::validatePlatform();
        } catch (\RuntimeException $e) {
            plgSystemKickYooAddonsHelper::adminNotice($e->getMessage());

            return false;
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
