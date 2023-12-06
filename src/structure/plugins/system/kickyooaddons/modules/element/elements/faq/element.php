<?php

use Joomla\CMS\Factory;
use YOOtheme\Str;

return [

	'transforms' => [

		'render' => function ($node) {

			$json['@context'] = 'https://schema.org';
			$json['@type'] = 'FAQPage';
			$json['@mainEntity'] = array();
			foreach ($node->children as $child) {
				$question = array();
				$question['@type'] = 'Question';
				$question['name'] = $child->props['question'];
				$question['acceptedAnswer']['@type'] = 'Answer';
				$question['acceptedAnswer']['text'] = str_replace(array("\n", "\r"), '', $child->props['answer']);
				$json['mainEntity'][] = $question;
			}

			Factory::getDocument()->addCustomTag('<script type="application/ld+json">' . json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>');
		},

	],

];
