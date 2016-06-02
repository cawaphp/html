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
use Cawa\Html\Forms\Fields\File;
use Cawa\Renderer\HtmlContainer;
use Cawa\Renderer\HtmlElement;

class Fieldset extends HtmlContainer
{
    /**
     * @param string $legend
     */
    public function __construct(string $legend = null)
    {
        parent::__construct('<fieldset>');

        if ($legend) {
            $this->add(HtmlElement::create('<legend>', $legend));
        }
    }

    /**
     * @var bool
     */
    private $isAdded = false;

    /**
     * @param Form $form
     */
    public function onAdd(Form $form)
    {
        $this->isAdded = true;
    }

    /**
     * @inheritDoc
     */
    public function add(ViewController ...$elements)
    {
        if ($this->isAdded) {
            throw new \LogicException("can't add element on fieldset when already fielset is already add on a form");
        }

        return parent::add(...$elements);
    }

    /**
     * @inheritDoc
     */
    public function addFirst(ViewController ...$elements)
    {
        if ($this->isAdded) {
            throw new \LogicException("can't add element on fieldset when already fielset is already add on a form");
        }

        return parent::add(...$elements);
    }

    /**
     * @return array|Fields\AbstractField[]
     */
    public function getFields() : array
    {
        if ($this->elements[0]->getTag() == '<legend>') {
            return array_slice($this->elements, 1);
        }

        return $this->elements;
    }
}
