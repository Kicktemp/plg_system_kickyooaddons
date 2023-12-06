<?php

namespace YOOtheme;

use DateTime;
use Joomla\CMS\Factory;
use Kicktemp\YOOaddons\Form\Src\FormApi;

return [

	'transforms' => [

		'render' => function ($node) {

			$app = Factory::getApplication();

			/**
			 * @var FormApi $controller
			 * @var Metadata  $meta
			 */
			list($controller, $meta) = app(FormApi::class, Metadata::class);

			$provider = (array) $node->props['provider'];
			$form = array();

			foreach (['subject', 'recipients', 'body', 'html', 'from', 'fromname', 'reply_tos', 'ccs', 'bccs', 'email_copy', 'subject_copy', 'body_copy', 'from_copy', 'fromname_copy','recipients_copy'] as $key)
			{
				$form[$key] = $node->props[$key] ?? null;
			}

			foreach (['subject', 'body', 'recipients', 'reply_tos', 'subject_copy', 'body_copy', 'from_copy', 'fromname_copy', 'recipients_copy'] as $key)
			{
				$value = $form[$key];
				$regex = '/{app\s(.*?)}/i';

				if (is_null($value) || !preg_match($regex, $value, $matches))
				{
					continue;
				}

				$matchesapp = explode(' ', $matches[1]);

				// We may not have a module style so fall back to the plugin default.
				if (!array_key_exists(1, $matchesapp))
				{
					$matchesapp[1] = '';
				}

				$appkey  = trim($matchesapp[0]);
				$default = trim($matchesapp[1]);

				$form[$key] = $app->getParams()->get($appkey, $default);
			}


			// check captcha is active
			foreach ($node->children as $child)
			{
				if ($child->props['type'] === 'captcha')
				{
					if (is_null($child->props['title']))
					{
						$child->props['title'] = $node->attrs['data-id'];
					}

					$form['captcha'] = $child->props['title'];
				}


				if ($child->props['type'] === 'honeypot')
				{
					if (is_null($child->props['title']))
					{
						$child->props['title'] = 'honey_' . $node->attrs['data-id'];
					}

					$time = (new DateTime())->format('U');
					$form['kick_honeypot'] = $time;
					$form['kick_honeypotfield'] = $child->props['title'];
					$form['kick_honeypoterror'] = $child->props['honeypot_error'];
					$form['kick_honeypotmessage'] = $child->props['honeypot_message'];
					$form['min_seconds'] = ($child->props['control_min_seconds']) ?? 1;
				}

				if ($child->props['type'] === 'file')
				{
					$node->props['attachment'] = true;
				}
			}

			$node->settings = $controller->encodeData(
				array_merge($provider, $form)
			);

			$node->form = [
				'action' => Url::route('theme/kickform/submit', [
					'hash' => $controller->getHash($node->settings),
				]),
			];

			$meta->set('script:form', ['src' => Path::get('../../app/form.min.js'), 'defer' => true, 'version' => '[VERSION]']);
		},

	],
];
