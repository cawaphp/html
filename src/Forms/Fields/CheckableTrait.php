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

namespace Cawa\Html\Forms\Fields;

use Cawa\Renderer\EmptyElement;
use Cawa\Renderer\HtmlContainer;

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
     * @return $this
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
        if ($this->getField()->hasAttribute('checked')) {
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
            $this->getField()->addAttribute('checked', 'checked');
        } else {
            $this->getField()->removeAttribute('checked');
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function renderCheckable()
    {
        $labelContent = $this->getLabel()->getContent();
        $field = $this->getField();

        if (!is_null($this->submitValue)) {
            $field->addAttribute('value', $this->submitValue);
        }

        $this->getLabel()->setContent($this->getField()->render() . ' ' . $labelContent);
        $this->setField(new EmptyElement());

        return HtmlContainer::render();
    }
}
