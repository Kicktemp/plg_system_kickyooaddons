<?php

namespace Kicktemp\YOOaddons\ContactSource\Src\Listener;

use Joomla\CMS\Router\SiteRouter;
use Joomla\Component\Contact\Site\Helper\RouteHelper;
use Joomla\Component\Tags\Site\Helper\RouteHelper as TagRouteHelper;
use YOOtheme\Arr;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;

class LoadTemplateUrl
{
    public SiteRouter $router;

    public function __construct(SiteRouter $router)
    {
        $this->router = $router;
    }

    public function handle(array $template): array
    {
        $url = '';

        try {
            switch ($template['type'] ?? '') {
                case 'com_contact.category':
                    $lang = $this->getLanguage($template);
                    $catid = $template['query']['catid'] ?? [];

                    if (isset($catid[0])) {
                        $url = RouteHelper::getCategoryRoute($catid[0], $lang);
                    }

                    break;
            }

            if ($url) {
                $template['url'] = (string) $this->router->build($url);
            }
        } catch (\Exception $e) {
            // ArticleHelper::query() throws exception if article "attribs" are invalid JSON
        }

        return $template;
    }

    /**
     * Fixes lowercase language code from "en-gb" to "en-GB".
     */
    protected function getLanguage(array $template): string
    {
        return preg_replace_callback(
            '/-\w{2}$/',
            fn($matches) => strtoupper($matches[0]),
            $template['query']['lang'] ?? '',
        );
    }
}
