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

use Cawa\Date\Date as DateObject;
use Cawa\Date\DateTime as DateTimeObject;

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
        if ($value instanceof DateObject || $value instanceof DateTimeObject) {
            $value = $value->format('Y-m-d\TH:i:s');
        } elseif (is_string($value) && $value) {
            $date = new DateTimeObject($value);
            $value = $date->format('Y-m-d\TH:i:s');
        }

        return parent::setValue($value);
    }
}
