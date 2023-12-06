<?php

namespace Kicktemp\YOOaddons\ContactSource\Src\Type;

use function YOOtheme\trans;

class CategoryContactQueryType
{
	/**
	 * @return array
	 */
	public static function config()
	{
		return [
			'fields' => [
				'categorycontact' => [
					'type'       => 'CategoryContact',
					'metadata'   => [
						'label' => trans('Contact Category'),
						'view'  => ['com_contact.category'],
						'group' => trans('Page'),
					],
					'extensions' => [
						'call' => __CLASS__ . '::resolve',
					],
				],
			],
		];
	}

	public static function resolve($root)
	{
		if (isset($root['category'])) {
			return $root['category'];
		}
	}
}
