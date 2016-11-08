<?php
declare (strict_types = 1);

namespace Cawa\Html\Tables\RowRenderer;

use Cawa\Html\Tables\Table;
use Cawa\Renderer\HtmlContainer;

class RowHeader
{
    public function __invoke(HtmlContainer $tr, array $data, Table $table)
    {
        if (isset($data['_rowheader']) && $data['_rowheader']) {
            $tr->addClass('rowheader');
        }

        return $tr;
    }
}
