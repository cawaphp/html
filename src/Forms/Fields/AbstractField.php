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
use Cawa\Renderer\HtmlContainer;
use Cawa\Renderer\HtmlElement;

abstract class AbstractField extends HtmlContainer
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

        $this->add($this->field);

        if (!is_null($label)) {
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
     * @return $this
     */
    public function setLabel($label) : self
    {
        $index = null;
        foreach ($this->elements as $i => $element) {
            if ($element === $this->label) {
                $index = $i;
            }
        }

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

        if (is_null($index)) {
            $this->addFirst($label);
        } else {
            $this->elements[$index] = $label;
        }

        return $this;
    }

    /**
     * @var HtmlElement
     */
    private $field;

    /**
     * @return HtmlElement
     */
    public function getField() : HtmlContainer
    {
        return $this->field;
    }

    /**
     * @param HtmlElement|null $field
     */
    protected function setField(HtmlElement $field = null)
    {
        $index = null;
        foreach ($this->elements as $i => $element) {
            if ($element === $this->field) {
                $index = $i;
            }
        }

        if (is_null($index)) {
            throw new \LogicException("Can't find field");
        }

        $this->elements[$index] = $field;
        $this->field = $field;
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
     * @return $this
     */
    public function setValidation(string $primitiveType) : self
    {
        $this->primitiveType = $primitiveType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $value) : parent
    {
        if ($this->label) {
            $this->label->setFor($value);
        }

        return parent::setId($value);
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
     * @return $this
     */
    public function setName(string $name) : self
    {
        $this->field->addAttribute('name', $name);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContent()
    {
        return $this->field->getContent();
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content) : parent
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
     * @return $this
     */
    public function setValue($value) : self
    {
        if (is_bool($value)) {
            $value = ($value == true) ? '1' : '0';
        }

        if (!is_null($value)) {
            $this->field->addAttribute('value', $value);
        }

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
     * @return $this
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
        return $this->field->hasAttribute('disabled');
    }

    /**
     * @param bool $disabled
     *
     * @return $this
     */
    public function setDisabled(bool $disabled = true)
    {
        if ($disabled) {
            $this->field->addAttribute('disabled', 'disabled');
        } else {
            $this->field->removeAttribute('disabled');
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isReadOnly() : bool
    {
        return $this->field->hasAttribute('readonly');
    }

    /**
     * @param bool $readOnly
     *
     * @return $this
     */
    public function setReadOnly(bool $readOnly = true)
    {
        if ($readOnly) {
            $this->field->addAttribute('readonly', 'readonly');
        } else {
            $this->field->removeAttribute('readonly');
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired() : bool
    {
        return $this->field->hasAttribute('required');
    }

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired(bool $required = true)
    {
        if ($required) {
            $this->field->addAttribute('required', 'required');
        } else {
            $this->field->removeAttribute('required');
        }

        return $this;
    }
}
