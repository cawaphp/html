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

use Cawa\Renderer\HtmlElement;

class Select extends AbstractField
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @param string $name
     * @param string $label
     * @param array $options
     */
    public function __construct(string $name, string $label = null, array $options = [])
    {
        parent::__construct('<select>', $name, $label);
        $this->options = $options;

        $isAssociative = array_keys($this->options) !== range(0, count($this->options) - 1);

        $this->addOption("", "&nbsp;");

        foreach ($this->options as $key => $value) {
            $this->addOption((string) $key, $isAssociative ? (string) $key : (string) $value);
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

        $this->getField()->add($option);

        return $this;
    }

    /**
     * @return bool
     */
    public function isMultiple() : bool
    {
        return $this->getField()->hasAttribute('multiple');
    }

    /**
     * @param bool $multiple
     *
     * @return $this
     */
    public function setMultiple(bool $multiple = true)
    {
        if ($multiple) {
            $this->getField()->addAttribute('multiple', 'multiple');
        } else {
            $this->getField()->removeAttribute('multiple');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) : parent
    {
        if (!in_array($value, array_keys($this->options)) && $value != '') {
            throw new \InvalidArgumentException(sprintf(
                "Invalid option value '%s' for select '%s'",
                $value,
                $this->getName()
            ));
        }

        /** @var HtmlElement $element */
        foreach ($this->getField()->elements as $element) {
            if ($element->getAttribute('value') == $value) {
                $element->addAttribute('selected', 'selected');
            } else {
                $element->removeAttribute('selected');
            }
        }

        return $this;
    }
}
