<?php

namespace Kicktemp\YOOaddons\Navigator\Src\Listener;

use YOOtheme\Config;
use YOOtheme\Metadata;
use YOOtheme\Path;
use YOOtheme\Translator;

class LoadCustomizerData
{
	public Config $config;
	public Metadata $metadata;
	public Translator $translator;

	public function __construct(Config $config, Metadata $metadata, Translator $translator)
	{
		$this->config = $config;
		$this->metadata = $metadata;
		$this->translator = $translator;
	}

	public function handle(): void
	{
		$this->metadata->set('script:kicktemp-customizer-core', ['src' => '~kickyooaddons_url/modules/Navigator/assets/customizer.min.js', 'defer' => true]);
	}
}
