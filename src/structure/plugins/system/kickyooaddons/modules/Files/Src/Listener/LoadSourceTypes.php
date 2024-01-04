<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Files\Src\Listener;

use Kicktemp\YOOaddons\Files\Src\Type;
use YOOtheme\Config;
use YOOtheme\Path;

class LoadSourceTypes
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

	public function handle($source): void
	{
        $rootDir = Path::relative(
            $this->config->get('app.rootDir'),
            $this->config->get('app.uploadDir'),
        );

		$query = [
			Type\KickfilesQueryType::config($rootDir)
		];

		$types = [
			['Kickfile', Type\KickfileType::config()],
		];

		foreach ($query as $args) {
			$source->queryType($args);
		}

		foreach ($types as $args) {
			$source->objectType(...$args);
		}
	}
}
