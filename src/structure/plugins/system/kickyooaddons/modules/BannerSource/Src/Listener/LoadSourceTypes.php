<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\BannerSource\Src\Listener;

use Kicktemp\YOOaddons\BannerSource\Src\Type;

class LoadSourceTypes
{
	public function handle($source): void
	{
		$query = [
			Type\CustomBannersQueryType::config(),
		];

		$types = [
			['Banner', Type\BannerType::config()],
		];

		foreach ($query as $args) {
			$source->queryType($args);
		}

		foreach ($types as $args) {
			$source->objectType(...$args);
		}
	}
}
