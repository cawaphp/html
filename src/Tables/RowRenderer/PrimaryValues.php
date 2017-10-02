<?php

declare(strict_types = 1);

namespace Cawa\Html\Tables\RowRenderer;

use Cawa\Html\Tables\Table;
use Cawa\Renderer\HtmlContainer;

class PrimaryValues
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name = 'data-primaries')
    {
        $this->name = $name;
    }

    /**
     * @param HtmlContainer $tr
     * @param array $data
     * @param Table $table
     *
     * @return HtmlContainer
     */
    public function __invoke(HtmlContainer $tr, array $data, Table $table) : HtmlContainer
    {
        $primaries = $table->getPrimaryValues($data);
        $tr->addAttribute($this->name, json_encode($primaries));

        return $tr;
    }
}
