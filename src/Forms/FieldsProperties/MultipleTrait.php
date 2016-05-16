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

namespace Cawa\Html\Forms\FieldsProperties;

use Cawa\Html\Forms\Fields\AbstractField;

/**
 * @mixin AbstractField
 */
trait MultipleTrait
{
    /**
     * @return bool
     */
    public function isMultiple() : bool
    {
        return $this->getField()->hasAttribute('multiple');
    }

    /**
     * @param bool $multiple
     *
     * @return $this
     */
    public function setMultiple(bool $multiple = true)
    {
        if ($multiple) {
            $this->getField()->addAttribute('multiple', 'multiple');
        } else {
            $this->getField()->removeAttribute('multiple');
        }

        return $this;
    }
}
