<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Cawa\Html\Tables;

use Cawa\Bootstrap\Tables\Column;
use Cawa\Core\Controller\Renderer\HtmlContainer;
use Cawa\Core\Controller\Renderer\HtmlElement;
use Cawa\Core\Controller\ViewController;

class Table extends HtmlContainer
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('<table>');
        $this->thead = HtmlContainer::create("<thead>");
        $this->tbody = HtmlContainer::create("<tbody>");

        $this->elements[] = $this->thead;
        $this->elements[] = $this->tbody;
    }

    /**
     * @var HtmlContainer
     */
    private $thead;

    /**
     * @var HtmlContainer
     */
    private $tbody;

    /**
     * @inheritdoc
     */
    public function add(ViewController $element)
    {
        if (!$element instanceof Column) {
            throw new \LogicException(sprintf("Invalid column type, got '%s'", get_class($element)));
        }

        $this->thead->add($element);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addFirst(ViewController $element)
    {
        if (!$element instanceof Column) {
            throw new \LogicException(sprintf("Invalid column type, got '%s'", get_class($element)));
        }

        $this->thead->addFirst($element);

        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColums()
    {
        return $this->thead->elements;
    }

    /**
     * @var array
     */
    private $data = [];

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data) : self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getPrimaryValues(array $data) : array
    {
        $return = array();
        /** @var Column $column */
        foreach ($this->thead->elements as $column) {
            if ($column->isPrimary()) {
                $return[$column->getId()] = $data[$column->getId()] ?? null;
            }
        }

        return $return;
    }

    /**
     * @return string
     */
    public function render()
    {
        foreach ($this->data as $row) {
            $tr = HtmlContainer::create("<tr>");

            /** @var Column $column */
            foreach ($this->thead->elements as $column) {

                if ($column->isVisible()) {
                    $td = HtmlElement::create("<td>");
                    $td->addClass($column->getClasses());
                    $content = $row[$column->getId()] ?? "";

                    if ($column->getRenderer()) {
                        $content = call_user_func($column->getRenderer(), $content, $this->getPrimaryValues($row));
                    }

                    $td->setContent((string)$content);
                    $tr->add($td);
                }
            }

            $this->tbody->add($tr);
        }

        $return = parent::render();

        return $return;
    }
}
