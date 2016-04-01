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

use Cawa\Core\Controller\Renderer\HtmlElement;

class Select extends AbstractField
{
    /**
     * @param string $name
     * @param string $label
     * @param array $options
     */
    public function __construct(string $name, string $label = null, array $options = [])
    {
        parent::__construct('<select>', $name, $label);

        $isAssociative = array_keys($options) !== range(0, count($options) - 1);

        $option = new HtmlElement('<option>');
        $this->getField()->add($option);

        foreach ($options as $key => $value) {
            $option = new HtmlElement('<option>');
            $option->setContent((string) $value);
            $option->addAttribute('value', $isAssociative ? (string) $key : (string) $value);

            $this->getField()->add($option);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) : parent
    {
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
