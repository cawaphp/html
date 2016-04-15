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

class Checkbox extends AbstractField
{
    use CheckableTrait;

    /**
     * @param string $name
     * @param string $label
     * @param string $submitValue
     */
    public function __construct(string $name, string $label, string $submitValue = null)
    {
        parent::__construct('<input />', $name, $label);
        $this->getField()->addAttribute('type', 'checkbox');

        $this->setSubmitValue($submitValue ?: '1');
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->renderCheckable();
    }
}
