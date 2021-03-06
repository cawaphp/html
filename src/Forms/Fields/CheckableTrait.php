<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Cawa\Html\Forms\Fields;

use Cawa\Renderer\Container;

/**
 * @mixin AbstractField
 */
trait CheckableTrait
{
    /**
     * @var string
     */
    private $submitValue;

    /**
     * @return string
     */
    public function getSubmitValue()
    {
        return $this->submitValue;
    }

    /**
     * @param string $submitValue
     *
     * @return $this|self
     */
    public function setSubmitValue($submitValue) : self
    {
        $this->submitValue = $submitValue;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if ($this->getField()->hasProp('checked')) {
            return $this->getSubmitValue();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) : parent
    {
        if ($this->getSubmitValue() == $value) {
            $this->getField()->addProp('checked');
        } else {
            $this->getField()->removeProp('checked');
        }

        return $this;
    }

    /**
     * @param bool $checked
     *
     * @return self
     */
    public function setChecked(bool $checked) : self
    {
        if ($checked) {
            $this->getField()->addProp('checked');
        } else {
            $this->getField()->removeProp('checked');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function layout() : Container
    {
        if (!is_null($this->submitValue)) {
            $this->getField()->addAttribute('value', $this->submitValue);
        }

        if ($this->getLabel()) {
            $label = clone $this->getLabel();
            $label->setContent($this->getField()->render() . ' ' . $label->getContent());

            return (new Container())->add($label);
        } else {
            return (new Container())->add($this->getField());
        }
    }
}
