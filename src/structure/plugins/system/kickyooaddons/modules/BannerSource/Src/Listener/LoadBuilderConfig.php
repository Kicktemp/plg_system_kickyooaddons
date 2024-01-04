<?php

namespace Kicktemp\YOOaddons\BannerSource\Src\Listener;

use Joomla\CMS\HTML\HTMLHelper;
use YOOtheme\Builder\BuilderConfig;
use Joomla\Component\Banners\Administrator\Helper\BannersHelper;
use YOOtheme\Config;

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
	    $options = BannersHelper::getClientOptions();

	    foreach ($options as $option)
	    {
		    $option->value = (int) $option->value;
	    }


	    $config->merge([
		    'banners.clients' => $options,

		    'banners.categories' => array_map(
			    fn($category) => ['value' => (string) $category->value, 'text' => $category->text],
			    HTMLHelper::_('category.options', 'com_banners')
		    ),
	    ]);
    }
}
