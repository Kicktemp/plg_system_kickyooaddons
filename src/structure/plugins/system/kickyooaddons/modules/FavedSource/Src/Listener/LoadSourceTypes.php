<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\FavedSource\Src\Listener;

use Kicktemp\YOOaddons\FavedSource\Src\Type;

class LoadSourceTypes
{
    public function handle($source): void
    {
        $query = [
            Type\CustomFavedQueryType::config(),
        ];

        foreach ($query as $args) {
            $source->queryType($args);
        }
    }
}
