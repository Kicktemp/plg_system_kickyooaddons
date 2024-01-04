<?php

namespace Kicktemp\YOOaddons\HubSpot\Src\Listener;

use YOOtheme\Config;
use YOOtheme\Metadata;
use YOOtheme\Path;

class LoadCustomizerData
{
	public Config $config;
	public Metadata $metadata;

	public function __construct(Config $config, Metadata $metadata)
	{
		$this->config = $config;
		$this->metadata = $metadata;
	}

	public function handle(): void
	{
		$this->config->addFile('customizer', Path::get('../../config/customizer.json'));
        $this->metadata->set('script:kicktemp-customizer-hubspot', ['src' => '~kickyooaddons_url/modules/HubSpot/assets/customizer.min.js', 'defer' => true]);
	}
}
