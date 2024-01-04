<?php

namespace Kicktemp\YOOaddons\Brevo\Src\Listener;

use YOOtheme\Config;
use YOOtheme\Metadata;
use YOOtheme\Path;
use YOOtheme\Translator;

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
        $this->metadata->set('script:kicktemp-customizer-brevo', ['src' => '~kickyooaddons_url/modules/Brevo/assets/customizer.min.js', 'defer' => true]);
	}
}
