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
use Cawa\Renderer\Container;
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
     * @return HtmlContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

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
     * @return $this|self
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
     * @return Container
     */
    protected function layout() : Container
    {
        $container = new Container();
        if ($this->label) {
            $container->add($this->label);
        }

        $container->add($this->container);

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->content = $this->layout()->render();

        return parent::render();
    }
}
