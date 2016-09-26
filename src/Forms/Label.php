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

namespace Cawa\Html\Forms;

use Cawa\Renderer\HtmlElement;

class Label extends HtmlElement
{
    /**
     * @param string $content
     */
    public function __construct(string $content = null)
    {
        parent::__construct('<label>');
        $this->setContent($content);
    }

    /**
     * @return null|string
     */
    public function getFor()
    {
        return $this->getAttribute('for');
    }

    /**
     * @param string $for
     *
     * @return $this|self
     */
    public function setFor(string $for) : self
    {
        return $this->addAttribute('for', $for);
    }
}
