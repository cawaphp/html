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

use Cawa\Controller\ViewController;
use Cawa\Intl\TranslatorFactory;
use Cawa\Renderer\HtmlContainer;
use Cawa\Renderer\HtmlElement;

class Table extends HtmlContainer
{
    use TranslatorFactory;

    /**
     *
     */
    public function __construct()
    {
        self::translator()->addFile(__DIR__ . '/../../lang/global', 'html');

        parent::__construct('<table>');
        $this->thead = (new HtmlContainer('<thead>'));
        $this->tbody = (new HtmlContainer('<tbody>'));

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
     * {@inheritdoc}
     */
    public function add(ViewController ...$elements)
    {
        foreach ($elements as $element) {
            if (!$element instanceof Column) {
                throw new \LogicException(sprintf("Invalid column type, got '%s'", get_class($element)));
            }

            $this->thead->add($element);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFirst(ViewController ...$elements)
    {
        foreach ($elements as $element) {
            if (!$element instanceof Column) {
                throw new \LogicException(sprintf("Invalid column type, got '%s'", get_class($element)));
            }

            $this->thead->addFirst($element);
        }

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
     * @return Column[]
     */
    public function getVisibleColumns() : array
    {
        $return = [];
        /** @var Column $column */
        foreach ($this->thead->elements as $column) {
            if ($column->isVisible() && $column->isRenderable()) {
                $return[] = $column;
            }
        }

        return $return;
    }

    //region RowActions

    /**
     * @var RowAction[]
     */
    private $rowActions = [];

    /**
     * @param RowAction $rowAction
     *
     * @return $this|self
     */
    public function addRowAction(RowAction $rowAction) : self
    {
        $this->rowActions[] = $rowAction;

        return $this;
    }

    //endregion

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
     * @return $this|self
     */
    public function addData(array $data) : self
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this|self
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
        $return = [];
        /** @var Column $column */
        foreach ($this->thead->elements as $column) {
            if ($column->isPrimary()) {
                $return[$column->getId()] = $data[$column->getId()] ?? null;
            }
        }

        return $return;
    }

    /**
     * @var callable[]
     */
    private $renderCallback;

    /**
     * @param callable $renderCallback
     *
     * @return $this|self
     */
    public function addRenderCallback(callable $renderCallback) : self
    {
        $this->renderCallback[] = $renderCallback;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        if (sizeof($this->data)) {
            // append row actions
            foreach ($this->rowActions as $i => $rowAction) {
                $this->add(
                    (new Column('row_action_' . $i, ''))
                        ->addRenderer(function ($content, Column $column, array $primaries) use ($rowAction) {
                            foreach ($primaries as $key => $value) {
                                if (is_null($value)) {
                                    continue;
                                }

                                unset($primaries[$key]);
                                $key = preg_replace_callback('/(?:[-_])(.?)/', function ($match) {
                                    return strtoupper($match[1]);
                                }, $key);
                                $primaries[$key] = (string) $value;
                            }

                            if (sizeof(array_filter($primaries)) == 0) {
                                return '';
                            }

                            if ($rowAction->getUri()) {
                                return $rowAction->setUri($rowAction->getUri()->addQueries($primaries))
                                    ->render();
                            } else {
                                return $rowAction->addAttribute('data-ids', json_encode($primaries))
                                    ->render();
                            }
                        })
                        ->addClass('row-action')
                        ->setHideable(false)
                );
            }

            foreach ($this->data as $row) {
                $tr = (new HtmlContainer('<tr>'));

                if (sizeof($this->thead->elements)) {
                    /** @var Column $column */
                    foreach ($this->thead->elements as $column) {
                        if ($column->isVisible() && $column->isRenderable()) {
                            $td = (new HtmlElement('<td>'));
                            $td->addClass($column->getClasses());
                            $content = $row[$column->getId()] ?? '';

                            if ($column->getRenderer()) {
                                foreach ($column->getRenderer() as $renderer) {
                                    $content = $renderer($content, $column, $this->getPrimaryValues($row), $row);
                                }
                            }

                            $td->setContent((string) $content);
                            $tr->add($td);
                        }
                    }
                } else {
                    foreach ($row as $col) {
                        $tr->add(new HtmlElement('<td>', $col));
                    }
                }

                if ($this->renderCallback) {
                    foreach ($this->renderCallback as $callback) {
                        $tr = call_user_func($callback, $tr, $row);
                    }
                }

                $this->tbody->add($tr);
            }
        } else {
            $this->tbody->add((new HtmlContainer('<tr>'))
                ->add((new HtmlElement('<td>'))
                    ->addAttribute('colspan', (string) sizeof($this->getVisibleColumns()))
                    ->setContent(self::trans('html.table/noResult'))
                    ->addClass('text-center')));
        }

        $return = parent::render();

        return $return;
    }
}
