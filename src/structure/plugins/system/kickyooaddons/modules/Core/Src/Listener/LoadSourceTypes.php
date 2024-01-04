<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\Core\Src\Listener;

use Kicktemp\YOOaddons\Core\Src\Type;


class LoadSourceTypes
{
    public function handle($source): void
    {
        $query = [
            Type\KickseoQueryType::config(),
        ];

        $types = [
            ['Kickseo', Type\KickseoType::config()],
            ['KickseoLocales', Type\KickseoLocalesType::config()],
        ];

        foreach ($query as $args) {
            $source->queryType($args);
        }

        foreach ($types as $args) {
            $source->objectType(...$args);
        }
    }
}
