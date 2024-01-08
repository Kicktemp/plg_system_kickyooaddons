<?php

namespace Kicktemp\YOOaddons\SectionSlideshow\Src\Listener;

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
		$this->config->addFile('customizer.panels.kicktemp-sectionslideshow-config', Path::get('../../config/kickyoosectionslideshow-config.json'));
	}
}
