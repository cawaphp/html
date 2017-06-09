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

namespace Cawa\Html;

use Cawa\Renderer\HtmlContainer;

class Link extends HtmlContainer
{
    use LinkTrait;

    /**
     * @param string|null $content
     * @param string|null $link
     */
    public function __construct(string $content = null, string $link = null)
    {
        parent::__construct('<a>', $content);
        if ($link) {
            $this->setHref($link);
        }
    }
}
