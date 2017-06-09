<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Cawa\Html\Tables\ColumnRenderer;

use Cawa\Html\Tables\Column;

abstract class AbstractRenderer
{
    /**
     * @param string|array $content
     * @param Column $column
     * @param array $primaryValues
     * @param array $data
     *
     * @return string
     */
    abstract public function __invoke($content, Column $column, array $primaryValues, array $data) : string;
}
