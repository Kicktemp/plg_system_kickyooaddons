<?php

use Joomla\CMS\Factory;
use YOOtheme\Str;

return [

	'transforms' => [

		'render' => function ($node) {

			$fields = array('brand','image','description','gtin8','sku','gtin13','gtin14','mpn');

			$json['@context'] = 'https://schema.org';
			$json['@type'] = 'Product';
			if ($node->props['productname'])
			{
				$json['name'] = $node->props['productname'];
			}

			foreach ($fields as $field)
			{
				if (isset($node->props[$field]) && $node->props[$field])
				{
					if ($field == 'image')
					{
						$node->props[$field] = JUri::root() . $node->props[$field];
					}
					$json[$field] = $node->props[$field];
				}
			}

			$json['offers']['@type'] = 'Offer';
			$json['offers']['url'] = $node->props['url'];
			$json['offers']['priceCurrency'] = $node->props['priceCurrency'];
			$json['offers']['price'] = $node->props['price'];
			$json['offers']['priceValidUntil'] = $node->props['priceValidUntil'];
			$json['offers']['availability'] = $node->props['availability'];
			$json['offers']['itemCondition'] = $node->props['itemCondition'];


			if ( $node->props['ratingValue']
			&& $node->props['ratingCount']
			&& $node->props['bestRating']
			&& $node->props['worstRating'] ) {
				$json['aggregateRating']['@type'] = 'AggregateRating';
				$json['aggregateRating']['ratingValue'] = $node->props['ratingValue'];
				$json['aggregateRating']['ratingCount'] = $node->props['ratingCount'];
				$json['aggregateRating']['bestRating'] = $node->props['bestRating'];
				$json['aggregateRating']['worstRating'] = $node->props['worstRating'];
			}

			if (count((array) $node->children))
			{
				$json['review'] = array();
				foreach ($node->children as $child) {
					if ($child->props['rating'] > $node->props['bestRating'] || $child->props['rating'] > $node->props['worstRating'])
					{
						continue;
					}
					$review = array();
					$review['@type'] = 'Review';
					$review['name'] = $child->props['review'];
					$review['reviewBody'] = $child->props['body'];
					$review['reviewRating']['@type'] = 'Rating';
					$review['reviewRating']['ratingValue'] = $child->props['rating'];
					$review['reviewRating']['bestRating'] = $node->props['bestRating'];
					$review['reviewRating']['worstRating'] = $node->props['worstRating'];
					$review['datePublished'] = $child->props['date'];
					$review['datePublished'] = $child->props['date'];
					$review['author']['@type'] = 'Person';
					$review['author']['name'] = $child->props['author'];
					$review['publisher']['@type'] = 'Organization';
					$review['publisher']['name'] = $child->props['publisher'];
					$json['review'][] = $review;
				}
			}

			Factory::getDocument()->addCustomTag('<script type="application/ld+json">' . json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>');
		},

	],

];
