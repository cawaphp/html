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
use Cawa\Html\Tables\RowAction as RowActionBase;

class RowAction extends AbstractRenderer
{
    /**
     * @var RowActionBase
     */
    private $rowAction;

    /**
     * @param RowActionBase $rowAction
     */
    public function __construct(RowActionBase $rowAction)
    {
        $this->rowAction = $rowAction;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($content, Column $column, array $primaryValues, array $data) : string
    {
        $primaries = [];

        foreach ($primaryValues as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            unset($primaryValues[$key]);
            $key = preg_replace_callback('/(?:[-_])(.?)/',
                function ($match) {
                    return strtoupper($match[1]);
                },
                $key);
            $primaries[$key] = (string) $value;
        }

        if (sizeof(array_filter($primaries)) == 0) {
            return '';
        }

        if ($this->rowAction->getUri()) {
            return $this->rowAction->setUri($this->rowAction->getUri()->addQueries($primaries))
                ->render();
        } else {
            return $this->rowAction->addAttribute('data-ids', json_encode($primaries))
                ->render();
        }
    }
}
