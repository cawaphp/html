<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Cawa\Html\Tables;

use Cawa\Controller\ViewController;
use Cawa\Html\Forms\Fields\Checkbox;
use Cawa\Html\Forms\Fields\Radio;
use Cawa\Html\Forms\Form;
use Cawa\Html\Tables\ColumnRenderer\RowAction as RowActionRenderer;
use Cawa\Intl\TranslatorFactory;
use Cawa\Renderer\HtmlContainer;
use Cawa\Renderer\HtmlElement;

class Table extends HtmlContainer
{
    use TranslatorFactory;

    //region Constants

    const SELECTION_RADIO = 'radio';
    const SELECTION_CHECKBOX = 'checkbox';

    //endregion

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
    public function getColumns()
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
     * @var callable[]
     */
    private $rowActionInit = [];

    /**
     * @return RowAction[]
     */
    public function getRowActions() : array
    {
        return $this->rowActions;
    }

    /**
     * @param RowAction $rowAction
     * @param callable $rowActionInit
     *
     * @return $this|Table
     */
    public function addRowAction(RowAction $rowAction, callable $rowActionInit = null) : self
    {
        $this->rowActions[] = $rowAction;
        $this->rowActionInit[] = $rowActionInit;

        return $this;
    }

    //endregion

    //region SelectionModel

    /**
     * @var string
     */
    private $selectionModel;

    /**
     * @param string $selection
     *
     * @return $this|Table
     */
    public function setSelectionModel(string $selection) : self
    {
        $this->selectionModel = $selection;

        $this->thead->add((new HtmlElement('<th>'))
            ->addClass('selection')
        );

        return $this;
    }

    /**
     * @var string
     */
    private $selectionName;

    /**
     * @return string
     */
    public function getSelectionName() : string
    {
        return $this->selectionName;
    }

    /**
     * @param string $selectionName
     *
     * @return Table
     */
    public function setSelectionName(string $selectionName) : Table
    {
        $this->selectionName = $selectionName;

        return $this;
    }

    /**
     * @var Form
     */
    private $selectionForm;

    /**
     * @return Form
     */
    public function getSelectionForm() : Form
    {
        return $this->selectionForm;
    }

    /**
     * @param Form $selectionForm
     *
     * @return Table
     */
    public function setSelectionForm(Form $selectionForm) : Table
    {
        $this->selectionForm = $selectionForm;

        return $this;
    }

    /**
     * @var string
     */
    private $selectionClass;

    /**
     * @return string
     */
    public function getSelectionClass() : string
    {
        return $this->selectionClass;
    }

    /**
     * @param string $selectionClass
     *
     * @return Table
     */
    public function setSelectionClass(string $selectionClass) : Table
    {
        $this->selectionClass = $selectionClass;

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
            if ($column instanceof Column && $column->isPrimary()) {
                $return[$column->getId()] = $data[$column->getId()] ?? null;
            }
        }

        return $return;
    }

    /**
     * @var callable[]
     */
    private $rowRenderers;

    /**
     * @param callable $rowRenderer
     *
     * @return $this|self
     */
    public function addRowRenderer(callable $rowRenderer) : self
    {
        $this->rowRenderers[] = $rowRenderer;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        if (sizeof($this->data)) {
            if (sizeof($this->thead->elements) == 0) {
                $keys = array_keys($this->data[0]);
                if (!is_numeric($keys[0])) {
                    foreach ($keys as $column) {
                        $this->add(new Column($column, $column));
                    }
                }
            }

            // append row actions
            foreach ($this->rowActions as $i => $rowAction) {
                $rowActionColumn = (new Column('row_action_' . $i, ''))
                    ->addRenderer(new RowActionRenderer($rowAction))
                    ->addClass('row-action')
                    ->setHideable(false);

                if (is_callable($this->rowActionInit[$i])) {
                    $rowActionColumn = $this->rowActionInit[$i]($rowActionColumn);
                }
                $this->add($rowActionColumn);
            }

            foreach ($this->data as $row) {
                $tr = (new HtmlContainer('<tr>'));

                if (sizeof($this->thead->elements)) {
                    if ($this->selectionModel) {
                        $primaries = $this->getPrimaryValues($row);
                        if (sizeof($primaries) == 1) {
                            $name = array_keys($primaries)[0];
                            $values = (string) $primaries[$name];
                        } else {
                            $name = implode('.', array_keys($primaries));
                            $values = json_encode($primaries);
                        }

                        if ($this->selectionName) {
                            $name = $this->selectionName;
                        }

                        if ($this->selectionModel == self::SELECTION_RADIO) {
                            $input = new Radio($name, '', $values);
                        } else {
                            $input = new Checkbox($name, '', $values);
                        }

                        if (isset($row['_selected'])) {
                            $input->setChecked(true);

                            if ($this->selectionClass) {
                                $tr->addClass($this->selectionClass);
                            }
                        }

                        if ($this->selectionForm) {
                            $this->selectionForm->add($input);
                        }

                        $tr->add((new HtmlContainer('<td>'))
                            ->add($input)
                        );
                    }

                    /** @var Column $column */
                    foreach ($this->thead->elements as $column) {
                        if ($column instanceof Column && $column->isVisible() && $column->isRenderable()) {
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

                if ($this->rowRenderers) {
                    foreach ($this->rowRenderers as $callback) {
                        $tr = call_user_func($callback, $tr, $row, $this);
                    }
                }

                $this->tbody->add($tr);
            }
        } else {
            $this->tbody->add((new HtmlContainer('<tr>'))
                ->add((new HtmlElement('<td>'))
                    ->addAttribute('colspan', (string) sizeof($this->getVisibleColumns()))
                    ->setContent(self::trans('html.list/noResult'))
                    ->addClass('text-center')));
        }

        $return = parent::render();

        return $return;
    }
}
