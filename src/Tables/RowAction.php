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

namespace Cawa\Html\Tables;

use Cawa\Html\Link;

class RowAction extends Link
{
    /**
     * @param string $name
     * @param string|null $link
     * @param string|null $icon
     */
    public function __construct(string $name, string $link = null, string $icon = null)
    {
        if ($icon) {
            $content = $icon ? '<i class="' . $icon . '"></i>' : $name;
            $this->addAttribute('title', $name);
        } else {
            $content = $name;
        }

        parent::__construct($content, $link);
    }

    /**
     * @return int
     */
    public function getActionIndex() : ?int
    {
        return $this->getAttribute('data-action-index') ? (int) $this->getAttribute('data-action-index') : null;
    }

    /**
     * @param int $index
     *
     * @return $this|self
     */
    public function setActionIndex(int $index = null) : self
    {
        if ($index) {
            return $this->addAttribute('data-action-index', (string) $index);
        } else {
            return $this->removeAttribute('data-index-action');
        }
    }
}
