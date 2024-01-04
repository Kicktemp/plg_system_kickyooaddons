<?php

namespace Kicktemp\YOOaddons\Form\Src\Listener;

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
			    'value' => 'title'
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

	    $config->merge([
		    'contacts.contactOrderOptions' => $contactOrders,

		    'contacts.categories' => array_map(
			    fn($category) => ['value' => (string) $category->value, 'text' => $category->text],
			    HTMLHelper::_('category.options', 'com_contact')
		    ),
	    ]);
    }
}
