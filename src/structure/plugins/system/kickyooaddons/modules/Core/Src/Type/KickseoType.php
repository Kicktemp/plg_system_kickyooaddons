<?php

namespace Kicktemp\YOOaddons\Core\Src\Type;

use function YOOtheme\trans;

class KickseoType
{
	/**
	 * @return array
	 */
	public static function config()
	{
		return [

			'fields' => [

				'title' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Page Title'),
						'filters' => ['limit'],
					],
				],

				'metadesc' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Meta Description'),
						'filters' => ['limit'],
					],
				],

				'base' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('KickSeo Base'),
						'filters' => ['limit'],
					],
				],

				'root' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('KickSeo Root'),
						'filters' => ['limit'],
					],
				],

				'current' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('KickSeo Current'),
						'filters' => ['limit'],
					],
				],

				'locale' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('KickSeo Locale'),
						'filters' => ['limit'],
					],
				],

				'localeAlternate' => [
					'type' => ['listOf' => 'KickseoLocales'],
					'metadata' => [
						'label' => trans('Content Languages'),
						'filters' => ['limit'],
					]
				],

			],

			'metadata' => [
				'type' => true,
				'label' => trans('Kick SEO'),
			],

		];
	}
}
