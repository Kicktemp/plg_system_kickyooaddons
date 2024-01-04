<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;

function getValues ($child)
{
	$json = array();

	for ($i = 1; $i <= 7; $i++)
	{
		$key = $child->props['key_' . $i];
		$value = $child->props['value_' . $i];
		$image = $child->props['image_' . $i];

		if (!is_null($image) && $image !== '')
		{
			$img = HTMLHelper::_('cleanImageURL', $image);
			$image = $img->url === null ? '' : htmlspecialchars($img->url, ENT_QUOTES, 'UTF-8');

			if (!is_null($image) && $image !== '' && File::exists($image))
			{
				$value = Uri::base() . $image;
			}
		}

		if (is_null($key) && !is_null($value) && $value !== '')
		{
			$json[] = $value;
		}
		else if (!is_null($key) && !is_null($value) && $value !== '')
		{
			$json[$key] = $value;
		}
	}

	if(count((array) $child->children))
	{
		foreach ($child->children as $nestedchild)
		{
			if(count((array) $child->children) <= 1)
			{
				$json[$child->props['key']] = getValues($nestedchild);
			} else {
				$json[$child->props['key']][] = getValues($nestedchild);
			}
		}
	}

	return $json;
}

return [

	'transforms' => [

		'render' => function ($node) {

			$json['@context'] = 'https://schema.org';
			$json['@type'] = $node->props['type'];

			if (count((array) $node->children))
			{
				foreach ($node->children as $child)
				{
					$json = array_merge($json, getValues($child));
				}
			}

			Factory::getDocument()->addCustomTag('<script type="application/ld+json">' . json_encode($json, $node->props['debug'] ? JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT : JSON_UNESCAPED_SLASHES) . '</script>');
		},

	],

];
