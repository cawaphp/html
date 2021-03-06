<?php

declare(strict_types = 1);

namespace Cawa\Html\Tables\RowRenderer;

use Cawa\Html\Tables\Table;
use Cawa\Renderer\HtmlContainer;

class RowHeader
{
    /**
     * @param HtmlContainer $tr
     * @param array $data
     * @param Table $table
     *
     * @return HtmlContainer
     */
    public function __invoke(HtmlContainer $tr, array $data, Table $table) : HtmlContainer
    {
        if (isset($data['_rowheader']) && $data['_rowheader']) {
            $tr->addClass('rowheader');
        }

        return $tr;
    }
}
