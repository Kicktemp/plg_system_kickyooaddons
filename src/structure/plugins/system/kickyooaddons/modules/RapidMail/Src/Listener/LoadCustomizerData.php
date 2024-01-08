<?php

namespace Kicktemp\YOOaddons\RapidMail\Src\Listener;

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
        $this->metadata->set('script:kicktemp-customizer-rapidmail', ['src' => '~kickyooaddons_url/modules/RapidMail/assets/customizer.min.js', 'defer' => true]);
	}
}
