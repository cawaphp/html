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

class Hidden extends AbstractField
{
    /**
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value = null)
    {
        parent::__construct('<input />', $name);
        $this->getField()->addAttribute('type', 'hidden');

        if (!is_null($value)) {
            $this->setValue($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->getField()->render();
    }
}
