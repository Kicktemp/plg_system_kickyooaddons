<?php

namespace Kicktemp\YOOaddons\Core\Src\Listener;

use YOOtheme\Config;
use YOOtheme\Metadata;
use YOOtheme\Path;
use YOOtheme\Translator;

class LoadCustomizerData
{
	public function __construct(Config $config, Metadata $metadata, Translator $translator)
	{
		$this->config = $config;
		$this->metadata = $metadata;
		$this->translator = $translator;
	}

	public function handle(): void
	{
		// add locale
		$locale = strtr($this->config->get('locale.code'), [
			'de_AT' => 'de_DE',
			'de_CH' => 'de_DE',
			'de_LI' => 'de_DE',
			'de_LU' => 'de_DE',
			'de_CH_informal' => 'de_DE',
			'de_DE_formal' => 'de_DE',
		]);

		$this->translator->addResource(Path::get("../languages/{$locale}.json"));
	}
}
