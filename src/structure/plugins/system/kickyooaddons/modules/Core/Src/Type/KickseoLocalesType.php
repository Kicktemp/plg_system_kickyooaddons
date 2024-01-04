<?php

namespace Kicktemp\YOOaddons\Core\Src\Type;

use function YOOtheme\trans;

class KickseoLocalesType
{
	/**
	 * @return array
	 */
	public static function config()
	{
		return [

			'fields' => [

				'lang_code' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Language Tag'),
						'filters' => ['limit'],
					],
				],

				'title' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Titel'),
						'filters' => ['limit'],
					],
				],

				'title_native' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Title in Native Language'),
						'filters' => ['limit'],
					],
				],

				'sef' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('URL Language Code'),
						'filters' => ['limit'],
					],
				],

				'image' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Image'),
						'filters' => ['limit'],
					],
				],

				'description' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Description'),
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

				'sitename' => [
					'type' => 'String',
					'metadata' => [
						'label' => trans('Custom Site Name'),
						'filters' => ['limit'],
					],
				],

				'published' => [
					'type' => 'Int',
					'metadata' => [
						'label' => trans('Published'),
						'filters' => ['limit'],
					],
				],

			],

			'metadata' => [
				'type' => true,
				'label' => trans('Content Languages'),
			],

		];
	}
}
