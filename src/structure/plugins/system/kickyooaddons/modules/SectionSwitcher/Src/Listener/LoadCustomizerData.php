<?php

namespace Kicktemp\YOOaddons\SectionSwitcher\Src\Listener;

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
		$this->config->addFile('customizer.panels.kicktemp-switcher-config', Path::get('../../config/switcher-config.json'));
	}
}
