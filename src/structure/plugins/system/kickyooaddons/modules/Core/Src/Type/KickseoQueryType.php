<?php

namespace Kicktemp\YOOaddons\Core\Src\Type;

use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use function YOOtheme\trans;


class KickseoQueryType
{
	/**
	 * @return array
	 */
	public static function config()
	{
		return [

			'fields' => [

				'kickseo' => [
					'type' => 'Kickseo',
					'metadata' => [
						'label' => trans('Kick SEO'),
						'group' => 'Kicktemp',
					],
					'extensions' => [
						'call' => __CLASS__ . '::resolve',
					],
				],

			],

		];
	}

	public static function resolve()
	{
		$uri = Uri::getInstance();
		$doc = Factory::getDocument();
		$lang = $doc->getLanguage();
		$languages = self::getLanguages($lang);

		return [
			'title' => $doc->getTitle(),
			'metadesc' => $doc->getDescription(),
			'base' => $uri->base(),
			'root' => $uri->root(),
			'current' => $uri->current(),
			'locale' => $lang,
			'localeAlternate' => $languages,
		];
	}

	public static function getLanguages($lang)
	{
		$return = [];
		$languages = LanguageHelper::getContentLanguages();

		foreach ($languages as $language)
		{
			if (strtolower($language->lang_code) === $lang)
			{
				continue;
			}

			array_push($return, $language);
		}

		return $return;
	}
}
