<?php

namespace Kicktemp\YOOaddons\ContactSource\Src\Listener;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Router;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Config;
use function YOOtheme\trans;

class LoadBuilderConfig
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param BuilderConfig $config
     */
    public function handle($config): void
    {
	    $contactOrders = [
		    [
			    'text' => 'Published',
			    'value' => 'publish_up'
		    ], [
			    'text' => 'Unpublished',
			    'value' => 'publish_down'
		    ], [
			    'text' => 'Created',
			    'value' => 'created'
		    ], [
			    'text' => 'Modified',
			    'value' => 'modified'
		    ], [
			    'text' => 'Alphabetical',
			    'value' => 'name'
		    ], [
			    'text' => 'Hits',
			    'value' => 'hits'
		    ], [
			    'text' => 'Contact Order',
			    'value' => 'ordering'
		    ], [
			    'text' => 'Random',
			    'value' => 'rand'
		    ]
	    ];

	    $languages = array_map(
		    fn($lang) => [
			    'value' => $lang->value == '*' ? '' : strtolower($lang->value),
			    'text' => $lang->text,
		    ],
		    HTMLHelper::_('contentlanguage.existing', true, true)
	    );

	    $languageField = [
		    'label' => trans('Limit by Language'),
		    'type' => 'select',
		    'defaultIndex' => 0,
		    'options' => [
			    ['evaluate' => 'config.languages'],
		    ],
		    'show' => '$customizer.languages[\'length\'] > 2 || lang',
	    ];

	    $templates = [
		    'com_contact.category' => [
			    'label' => trans('Category Contact'),
			    'fieldset' => [
				    'default' => [
					    'fields' => [
						    'catid' => ($category = [
							    'label' => trans('Limit by Categories'),
							    'description' => trans(
								    'The template is only assigned to articles from the selected categories. Articles from child categories are not included. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple categories.'
							    ),
							    'type' => 'select',
							    'default' => [],
							    'options' => [['evaluate' => 'yootheme.builder["contacts.categories"]']],
							    'attrs' => [
								    'multiple' => true,
								    'class' => 'uk-height-small',
							    ],
						    ]),
						    'tag' => ($tag = [
                                'label' => trans('Limit by Tags'),
                                'description' => trans(
                                    'The template is only assigned to articles with the selected tags. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple tags.'
                                ),
                                'type' => 'select',
                                'default' => [],
                                'options' => [['evaluate' => 'yootheme.builder.tags']],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ]),
						    'pages' => [
							    'label' => trans('Limit by Page Number'),
							    'description' => trans(
								    'The template is only assigned to the selected pages.'
							    ),
							    'type' => 'select',
							    'options' => [
								    trans('All pages') => '',
								    trans('First page') => 'first',
								    trans('All except first page') => 'except_first',
							    ],
						    ],
						    'lang' => $languageField,
					    ],
				    ],
			    ],
		    ]
	    ];


	    $config->merge([
		    'templates' => $templates,

		    'contacts.contactOrderOptions' => $contactOrders,

		    'contacts.categories' => array_map(
			    fn($category) => ['value' => (string) $category->value, 'text' => $category->text],
			    HTMLHelper::_('category.options', 'com_contact')
		    ),
	    ]);
    }
}
