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

class Text extends AbstractField
{
    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct('<input />', $name, $label);
        $this->getField()->addAttribute('type', 'text');
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->getField()->getAttribute('size');
    }

    /**
     * @param int $size
     *
     * @return $this|self
     */
    public function setSize(int $size) : self
    {
        $this->getField()->addAttribute('size', (string) $size);

        return $this;
    }

}
