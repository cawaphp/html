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
use Cawa\Renderer\HtmlContainer;
use Cawa\Renderer\HtmlElement;

class Select extends AbstractField implements MultipleValueInterface
{
    use MultipleTrait;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var HtmlElement[]
     */
    protected $optionsElements = [];

    /**
     * @var HtmlElement[]
     */
    protected $allOptions = [];

    /**
     * @var bool
     */
    protected $checkValue = true;

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
     * @param string|array $value
     * @param bool $autoAppend
     *
     * @return HtmlElement
     */
    protected function addOption(string $key, $value, bool $autoAppend = true) : HtmlElement
    {
        if (is_array($value)) {
            $group = $this->addGroup($key);

            foreach ($value as $currentKey => $currentValue) {
                $group->add($this->addOption((string) $currentKey, $currentValue, false));
            }

            return $group;
        } else {
            $option = new HtmlElement('<option>');
            $option->setContent((string) $value);
            $option->addAttribute('value', $key);

            $this->options[$key] = $value;
            $this->optionsElements[$key] = $option;

            if ($autoAppend) {
                $this->allOptions[$key] = $option;
            }

            return $option;
        }
    }

    /**
     * @param string $key
     *
     * @return HtmlContainer
     */
    protected function addGroup(string $key) : HtmlContainer
    {
        $group = (new HtmlContainer('<optgroup>'))
            ->addAttribute('label', $key);

        $this->allOptions['group-' . $key] = $group;

        return $group;
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
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($this->optionsElements as $element) {
            $element->removeAttribute('selected');
        }

        foreach ($value as $currentValue) {
            if ($currentValue == '') {
                continue;
            }

            if ($this->checkValue) {
                if (!in_array($currentValue, array_keys($this->options))) {
                    throw new \InvalidArgumentException(sprintf(
                        "Invalid option value '%s' for select '%s'",
                        $currentValue,
                        $this->getName()
                    ));
                }
            }

            /** @var HtmlElement $element */
            foreach ($this->optionsElements as $element) {
                if ($element->getAttribute('value') == $currentValue) {
                    $element->addAttribute('selected', 'selected');
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function layout() : Container
    {
        $this->getField()->setContent(
            (new Container())
                ->add(...array_values($this->allOptions))
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
            if (!is_null($this->getValue())) {
                $this->optionsElements['']->setRenderable(false);
            }
        }

        return parent::render();
    }
}
