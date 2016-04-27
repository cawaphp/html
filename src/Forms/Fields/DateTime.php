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

class DateTime extends AbstractField
{
    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct('<input />', $name, $label);
        $this->getField()->addAttribute('type', 'datetime-local');
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value) : parent
    {
        if ($value instanceof \Cawa\Date\DateTime) {
            $value = $value->formatTz('Y-m-d\TH:i:s');
        } elseif (is_string($value) && $value) {
            $date = new \Cawa\Date\DateTime($value);
            $value = $date->formatTz('Y-m-d\TH:i:s');
        }

        return parent::setValue($value);
    }
}
