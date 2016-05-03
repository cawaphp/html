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

namespace Cawa\Html\Forms;

use Cawa\Controller\ViewController;
use Cawa\Renderer\HtmlContainer;
use Cawa\Renderer\HtmlElement;

class Group extends HtmlContainer
{
    /**
     * @param string|null $label
     */
    public function __construct(string $label = null)
    {
        parent::__construct('<div>');

        $this->container = new HtmlContainer('<div>');
        $this->elements[] = $this->container;

        if ($label) {
            $this->setLabel($label);
        }
    }

    /**
     * @var HtmlContainer
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function add(ViewController ...$elements)
    {
        $this->container->add(...$elements);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFirst(ViewController ...$elements)
    {
        $this->container->addFirst(...$elements);

        return $this;
    }

    /**
     * Call by Form on populateValue
     *
     * @return array|Fields\AbstractField[]
     */
    public function getFields() : array
    {
        return $this->container->elements;
    }
    /**
     * @var Label|HtmlElement
     */
    private $label;

    /**
     * @return Label|HtmlElement
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param Label|HtmlElement|string $label
     *
     * @return $this
     */
    public function setLabel($label) : self
    {
        $index = $this->getIndex($this->label);

        if (!$label instanceof Label && !$label instanceof HtmlElement) {
            $label = new Label($label);
        }

        $this->label = $label;

        if (is_null($index)) {
            array_unshift($this->elements, $label);
        } else {
            $this->elements[$index] = $label;
        }

        return $this;
    }

    /**
     * @return HtmlContainer
     */
    public function getField()
    {
        return $this->container;
    }

    /**
     * @param HtmlContainer|null $field
     *
     * @return $this
     */
    protected function setField(HtmlContainer $field = null) : self
    {
        $index = $this->getIndex($this->container);
        $this->container = $field;

        if (is_null($index)) {
            array_unshift($this->elements, $field);
        } else {
            $this->elements[$index] = $field;
        }

        return $this;
    }
}
