<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       [AUTHOR_URL]
 */

namespace Kicktemp\YOOaddons\ContactSource\Src\Listener;

use Kicktemp\YOOaddons\ContactSource\Src\Type;
use YOOtheme\Builder\Joomla\Fields\FieldsHelper;
use YOOtheme\Builder\Joomla\Fields\Type as FieldType;
use function YOOtheme\trans;

class LoadSourceTypes
{
	public function handle($source): void
	{
		$query = [
			Type\CategoryContactQueryType::config(),
			Type\CustomContactsQueryType::config(),
		];

		$types = [
			['CategoryContact', Type\CategoryContactType::config()],
			['CategoryContactParams', Type\CategoryContactParamsType::config()],
			['KickContact', Type\KickContactType::config()],
		];

		$fieldTypes = [
			'KickContact' => 'com_contact.contact',
		];

		foreach ($query as $args) {
			$source->queryType($args);
		}

		foreach ($types as $args) {
			$source->objectType(...$args);
		}

		foreach ($fieldTypes as $type => $context) {
			// has custom fields?
			if ($fields = FieldsHelper::getFields($context)) {
				$this->configFields($source, $type, $context, $fields);
			}
		}
	}

	protected function configFields($source, $type, $context, array $fields): void
	{
		// add field on type
		$source->objectType(
			$type,
			[
				'fields' => [
					'field' => [
						'type' => ($fieldType = "{$type}Fields"),
						'metadata' => [
							'label' => trans('Fields'),
						],
						'extensions' => [
							'call' => FieldType\FieldsType::class . '::field',
						],
					],
				],
			]
		);

		// configure field type
		$source->objectType($fieldType, FieldType\FieldsType::config($source, $type, $context, $fields));
	}
}
