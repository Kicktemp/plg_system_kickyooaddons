<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Str;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

return [

	'transforms' => [

		'render' => function ($node) {

			$doc = Factory::getDocument();

			if ($node->props['og_url'])
			{
				$doc->setMetaData('og:url', Uri::current(), 'property');
			}

			if (!is_null($fbid = $node->props['facebookid']) && $fbid !== '')
			{
				$doc->setMetaData('fb:app:id', $fbid, 'property');
			}


			if (count((array) $node->children))
			{
				foreach ($node->children as $child)
				{
					$value = '';
					$key = $child->props['property'];
					$image = $child->props['image'];
					$content = $child->props['content'];
					$check_exists = $child->props['check_exists'];

					if ((is_null($key) && $key === '') || (is_null($image) && $image === '' && is_null($content) && $content === ''))
					{
						continue;
					}

					if (!is_null($image) && $image !== '')
					{
						$img = HTMLHelper::_('cleanImageURL', $image);
						$image = $img->url === null ? '' : htmlspecialchars($img->url, ENT_QUOTES, 'UTF-8');

						if (!is_null($image) && $image !== '' && File::exists($image))
						{
							$value = Uri::base() . $image;
						}
					}

					if (!is_null($content) && $content !== '')
					{
						$value = $content;
					}

					if (!$check_exists)
					{
						$metaData['property'] = $key;
						$metaData['content'] = $value;

						$doc->addCustomTag('<meta ' . ArrayHelper::toString($metaData) . '>');
					}
					elseif (!$doc->getMetaData($key, 'property'))
					{
						$doc->setMetaData($key, $value, 'property');
					}
				}
			}

			return $node->props['debug'];

		},

	],

];
