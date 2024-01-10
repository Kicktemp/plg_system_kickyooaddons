<?php

namespace YOOtheme;

use DateTime;
use Joomla\CMS\Factory;
use Kicktemp\YOOaddons\HubSpot\Src\HubSpotApi;

return [

	'transforms' => [

		'render' => function ($node, $params) {
            if ((!isset($node->attrs['data-id']) || $node->attrs['data-id'] === '')
                && (!empty($params['prefix']) || !empty($params['parent']))
            )
            {
                $prefix = empty($params['data-id']) ? "{$params['prefix']}#" : "{$params['data-id']}-";

                $node->attrs = $node->attrs ?? [];
                $node->attrs['data-id'] = $params['data-id'] = $prefix . $params['i'];
            }

			$app = Factory::getApplication();

            /**
             * @var HubSpotApi $controller
             * @var Metadata  $meta
             */
            list($controller, $meta) = app(HubSpotApi::class, Metadata::class);

            $provider = (array) $node->props['provider'];
            $form = array();

			if ($node->props['captcha'] === 'captcha' || $node->props['captcha'] === 'honeypotandcaptcha')
			{
				$node->props['captchatitle'] = 'captcha_' . $node->id;
				$form['captcha'] = 'captcha_' . $node->id;
			}

			if ($node->props['captcha'] === 'honeypot' || $node->props['captcha'] === 'honeypotandcaptcha')
			{

                if (is_null($node->props['honeypot_id']))
                {
                    $node->props['honeypot_id'] = 'honey_' . $node->attrs['data-id'];
                }

				$time = (new DateTime())->format('U');
				$form['kick_honeypot'] = $time;
				$form['min_seconds'] = ($node->props['control_min_seconds']) ?? 1;
                $form['kick_honeypotfield'] = $node->props['honeypot_id'];
                $form['kick_honeypoterror'] = $node->props['honeypot_error'];
                $form['kick_honeypotmessage'] = $node->props['honeypot_message'];

				// Fake Field
                $node->props['honeypottitle'] = $node->props['honeypot_id'];
			}

			// HubSpot
			if (isset($node->props['hubspot_guid']->fieldGroups))
			{
				foreach ($node->props['hubspot_guid']->fieldGroups as $group)
				{
					if (isset($group->fields ))
					{
						foreach ($group->fields as $field)
						{
							$form['hubspot_fields'][$field->name] = $field->objectTypeId;
						}
					}
				}
			}

			if (isset($node->props['hubspot_guid']->legalConsentOptions))
			{
				$form['legalConsentOptions'] = $node->props['hubspot_guid']->legalConsentOptions;
			}

			if (isset($node->props['hubspot_guid']->id))
			{
				$form['guid'] = $node->props['hubspot_guid']->id;
			}

            $node->settings = $controller->encodeData(
                array_merge($provider, $form)
            );

            $node->form = [
                'action' => Url::route('theme/kickhubspot/hubsubmit', [
                    'hash' => $controller->getHash($node->settings),
                ]),
            ];

			$meta->set('script:form', ['src' => Path::get('../../../Form/app/form.min.js'), 'defer' => true]);
		},

	],
];
