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

        $option = new HtmlElement('<option>');
        $this->getField()->add($option);

        foreach ($this->options as $key => $value) {
            $option = new HtmlElement('<option>');
            $option->setContent((string) $value);
            $option->addAttribute('value', $isAssociative ? (string) $key : (string) $value);

            $this->getField()->add($option);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setRequired(bool $required = true)
    {
        array_shift($this->getField()->elements);
        return parent::setRequired($required);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) : parent
    {
        if (!in_array($value, array_keys($this->options))) {
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
