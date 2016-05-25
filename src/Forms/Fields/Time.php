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

use Cawa\Date\Time as TimeObject;

class Time extends AbstractField
{
    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct('<input />', $name, $label);
        $this->getField()->addAttribute('type', 'time');
    }


    /**
     * {@inheritdoc}
     */
    public function setValue($value) : parent
    {
        if ($value instanceof TimeObject) {
            $value = $value->format();
        } elseif (is_string($value) && $value) {
            $date = new TimeObject($value);
            $value = $date->format();
        }

        return parent::setValue($value);
    }
}
