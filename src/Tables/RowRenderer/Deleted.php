<?php
declare (strict_types = 1);

namespace Cawa\Html\Tables\RowRenderer;

use Cawa\Date\DateTime;
use Cawa\Renderer\HtmlContainer;
use Cawa\Renderer\HtmlElement;

class Deleted
{
    public function __invoke(HtmlContainer $tr, array $data)
    {
        if (isset($data['_deleted']) && $data['_deleted']) {
            $tr->addClass('deleted');

            /** @var HtmlElement $current */
            foreach ($tr->getElements() as $current) {
                if ($current->hasClass('row-action') && stripos($current->getContent(), 'fa-trash') !== false) {
                    if ($data['_deleted'] instanceof DateTime) {
                        $current->setContent('<i title="' . ($data['_deleted']->display()) .
                            '" class="fa fa-info-circle" aria-hidden="true"></i>');
                    } else {
                        $current->setContent('');
                    }
                }
            }
        }

        return $tr;
    }
}
