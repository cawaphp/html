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

use Cawa\Html\Forms\FieldsProperties\MultipleTrait;
use Cawa\Html\Forms\FieldsProperties\MultipleValueInterface;
use Cawa\Renderer\Container;
use Cawa\Renderer\HtmlElement;

class Select extends AbstractField implements MultipleValueInterface
{
    use MultipleTrait;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var HtmlElement[]
     */
    private $optionsElements = [];

    /**
     * @param string $name
     * @param string $label
     * @param array $options
     */
    public function __construct(string $name, string $label = null, array $options = [])
    {
        parent::__construct('<select>', $name, $label);

        $isAssociative = array_keys($options) !== range(0, count($options) - 1);
        $this->addOption('', '&nbsp;');

        foreach ($options as $key => $value) {
            $this->addOption($isAssociative ? (string) $key : (string) $value, $value);
        }
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    protected function addOption(string $key, string $value) : self
    {
        $option = new HtmlElement('<option>');
        $option->setContent((string) $value);
        $option->addAttribute('value', $key);

        $this->options[$key] = $value;
        $this->optionsElements[$key] = $option;

        return $this;
    }

    /**
     * @return string|array
     */
    public function getValue()
    {
        $return = [];

        /** @var HtmlElement $element */
        foreach ($this->optionsElements as $element) {
            if ($element->getAttribute('selected') == 'selected') {
                $return[] = $element->getAttribute('value');
            }
        }

        if ($this->isMultiple()) {
            return $return;
        } else {
            return $return[0] ?? null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) : parent
    {
        $isArray = is_array($value);
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $currentValue) {
            if ($currentValue == '') {
                continue;
            }

            if (!in_array($currentValue, array_keys($this->options))) {
                throw new \InvalidArgumentException(sprintf(
                    "Invalid option value '%s' for select '%s'",
                    $currentValue,
                    $this->getName()
                ));
            }

            /** @var HtmlElement $element */
            foreach ($this->optionsElements as $element) {
                if ($element->getAttribute('value') == $currentValue) {
                    $element->addAttribute('selected', 'selected');
                } elseif (!$isArray) {
                    $element->removeAttribute('selected');
                }
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function layout() : Container
    {
        $this->getField()->setContent(
            (new Container())
                ->add(...array_values($this->optionsElements))
                ->render()
        );
        return parent::layout();
    }

    /**
     * @return string
     */
    public function render()
    {
        if ($this->isRequired() == true || $this->isMultiple() == true) {
            if ($this->getValue()) {
                $this->optionsElements['']->setRenderable(false);
            }
        }

        return parent::render();
    }
}
