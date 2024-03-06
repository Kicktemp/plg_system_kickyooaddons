<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [AUTHOR_URL]
 * @license    [LICENSE]
 * @link       [AUTHOR_URL]
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

abstract class plgSystemKickYooAddonsHelper
{
    const MIN_PHP_VERSION = '7.4';
    const MIN_YTP_VERSION = '4.0.0';
    const MAX_YTP_VERSION = '4.3.99';
    const MIN_JOOMLA_VERSION = '4.0';

    public static function adminNotice(string $message): void
    {
        $app = Factory::getApplication();

        if ($app->isClient('administrator')) {
            $app->enqueueMessage($message, 'warning');
        }
    }

    public static function validatePlatform()
    {
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, 'lt')) {
            throw new \RuntimeException(
                sprintf(
                    '[PROJECT_NAME] plugin requires a more recent version of PHP, please update PHP to v%s or later.',
                    self::MIN_PHP_VERSION
                )
            );
        }

        if (version_compare(JVERSION, self::MIN_JOOMLA_VERSION, 'lt')) {
            throw new \RuntimeException(
                sprintf(
                    '[PROJECT_NAME] plugin requires a more recent version of Joomla, please update Joomla to v%s or later.',
                    self::MIN_JOOMLA_VERSION
                )
            );
        }

        $theme = simplexml_load_file(JPATH_ROOT . '/templates/yootheme/templateDetails.xml');
        $themeVersion = (string) $theme->version;

        if ($theme === null || version_compare($themeVersion, self::MIN_YTP_VERSION, 'lt')) {
            throw new \RuntimeException(
                sprintf(
                    '[PROJECT_NAME] plugin requires YOOtheme Pro, please install or activate YOOtheme Pro v%s or later.',
                    self::MIN_YTP_VERSION
                )
            );
        }

        if (version_compare($themeVersion, self::MAX_YTP_VERSION, 'gt')) {
            throw new \RuntimeException(
                sprintf(
                    '[PROJECT_NAME] plugin does not support YOOtheme Pro v%s, please update [PROJECT_NAME] or contact support for further help.',
                    $themeVersion
                )
            );
        }
    }

    // checks if the theme being installed is compatible with the plugin
    public static function preinstallThemeCheck(SimpleXMLElement $manifest): void
    {
        if ((string) $manifest->packagename !== 'yootheme') {
            return;
        }

        $version = (string) $manifest->version;

        if (
            version_compare($version, self::MIN_YTP_VERSION, 'lt') ||
            version_compare($version, self::MAX_YTP_VERSION, 'gt')
        ) {
            throw new \RuntimeException(
                sprintf(
                    'YOOtheme Pro v%s is not supported by [PROJECT_NAME] plugin, disable Essentials before retrying.',
                    $version
                )
            );
        }
    }
}
