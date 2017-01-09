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

namespace Cawa\Html\Forms\Fields;

use Cawa\Html\Forms\Label;
use Cawa\Renderer\Container;
use Cawa\Renderer\HtmlContainer;
use Cawa\Renderer\HtmlElement;

abstract class AbstractField extends HtmlElement
{
    /**
     * @param string $tag
     * @param string $label
     * @param string $name
     */
    public function __construct(string $tag, string $name = null, string $label = null)
    {
        parent::__construct('<div>');

        $this->field = new HtmlContainer($tag);
        if ($name) {
            $this->setName($name);
        }

        if ($label) {
            $this->setLabel($label);
        }
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
        if (!$label instanceof Label && !$label instanceof HtmlElement) {
            $label = new Label($label);
        }

        $this->label = $label;

        if (!$this->field->getId() && $this->label instanceof Label) {
            $this->label->setFor($this->field->generateId()->getId());
        }

        if (!$this->getPlaceholder() && $this->label->getContent()) {
            $this->setPlaceholder($this->label->getContent());
        }

        return $this;
    }

    /**
     * @var HtmlElement
     */
    private $field;

    /**
     * @return HtmlContainer
     */
    public function getField() : HtmlContainer
    {
        return $this->field;
    }

    /**
     * @var string
     */
    private $primitiveType;

    /**
     * @return string|null
     */
    public function getPrimitiveType()
    {
        return $this->primitiveType;
    }

    /**
     * @param string $primitiveType
     *
     * @return $this|self
     */
    public function setValidation(string $primitiveType = null) : self
    {
        $this->primitiveType = $primitiveType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->field->getAttribute('id');
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $value) : parent
    {
        if ($this->label) {
            $this->label->setFor($value);
        }

        $this->field->setId($value);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->field->getAttribute('name');
    }

    /**
     * @param string $name
     *
     * @return $this|self
     */
    public function setName(string $name) : self
    {
        $this->field->addAttribute('name', $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->field->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content) : parent
    {
        $this->field->setContent($content);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getValue()
    {
        return $this->field->getAttribute('value');
    }

    /**
     * @param mixed $value
     *
     * @return $this|self
     */
    public function setValue($value) : self
    {
        if (is_bool($value)) {
            $value = ($value == true) ? '1' : '0';
        }

        $this->field->addAttribute('value', !is_null($value) ? (string) $value : '');

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlaceholder()
    {
        return $this->field->getAttribute('placeholder');
    }

    /**
     * @param string $name
     *
     * @return $this|self
     */
    public function setPlaceholder(string $name = null) : self
    {
        if (is_null($name)) {
            $this->field->removeAttribute('placeholder');
        } else {
            $this->field->addAttribute('placeholder', $name);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled() : bool
    {
        return $this->field->hasProp('disabled');
    }

    /**
     * @param bool $disabled
     *
     * @return $this|self
     */
    public function setDisabled(bool $disabled = true)
    {
        if ($disabled) {
            $this->field->addProp('disabled');
        } else {
            $this->field->removeProp('disabled');
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isReadOnly() : bool
    {
        return $this->field->hasProp('readonly');
    }

    /**
     * @param bool $readOnly
     *
     * @return $this|self
     */
    public function setReadOnly(bool $readOnly = true)
    {
        if ($readOnly) {
            $this->field->addProp('readonly');
        } else {
            $this->field->removeProp('readonly');
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired() : bool
    {
        return $this->field->hasProp('required');
    }

    /**
     * @param bool $required
     *
     * @return $this|self
     */
    public function setRequired(bool $required = true)
    {
        if ($required) {
            $this->field->addProp('required');
        } else {
            $this->field->removeProp('required');
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

        $container->add($this->field);

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    public function renderOuter() : array
    {
        $this->content = $this->layout()->render();

        return parent::renderOuter();
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
