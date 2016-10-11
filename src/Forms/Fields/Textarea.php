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

class Textarea extends AbstractField
{
    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct('<textarea>', $name, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) : parent
    {
        return $this->setContent($value);
    }
}
