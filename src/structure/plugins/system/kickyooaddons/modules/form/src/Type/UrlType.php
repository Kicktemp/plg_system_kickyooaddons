<?php

namespace Kicktemp\YOOaddons\Form\Src\Type;

use function YOOtheme\trans;

class UrlType
{
	/**
	 * @return array
	 */
	public static function config()
	{
		return [

			'fields' => [

				'base' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Website Url: Base'),
						'filters' => ['limit'],
					],
				],

				'root' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Website Url: Root'),
						'filters' => ['limit'],
					],
				],

				'current' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Website Url: Current'),
						'filters' => ['limit'],
					],
				],

			],

			'metadata' => [
				'type' => true,
				'label' => 'Website Url',
			],

		];
	}
}
