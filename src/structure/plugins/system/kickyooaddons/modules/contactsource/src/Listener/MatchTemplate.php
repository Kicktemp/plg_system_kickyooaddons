<?php

namespace Kicktemp\YOOaddons\ContactSource\Src\Listener;

use Joomla\CMS\Document\Document;

class MatchTemplate
{
    public Document $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function handle($view, $tpl): ?array
    {
        if ($tpl) {
            return null;
        }

        $context = $view->get('context');

        if ($context === 'com_contact.category') {
            $category = $view->get('category');
            $pagination = $view->get('pagination');

            return [
                'type' => $context,
                'query' => [
                    'catid' => $category->id,
                    'tag' => $view->get('State')->get('filter.tag', []),
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->document->language,
                ],
                'params' => [
                    'category' => $category,
                    'items' => $view->get('items'),
                    'pagination' => $pagination,
                ],
            ];
        }

        return null;
    }
}
