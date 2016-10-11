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

class Submit extends AbstractField
{
    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        parent::__construct('<input />');
        $this->getField()->addAttribute('type', 'submit');

        $this->setValue($value);
    }
}
