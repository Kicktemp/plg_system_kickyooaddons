<?php

namespace Kicktemp\YOOaddons\Core\Src\Type;

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
						'label' => 'Base',
						'filters' => ['limit'],
					],
				],

				'root' => [
					'type' => 'String',
					'metadata' => [
						'label' => 'Root',
						'filters' => ['limit'],
					],
				],

				'current' => [
					'type' => 'String',
					'metadata' => [
						'label' => 'Current',
						'filters' => ['limit'],
					],
				],

			],

			'metadata' => [
				'type' => true,
				'label' => 'Url',
			],

		];
	}
}
