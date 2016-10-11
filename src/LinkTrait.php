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

namespace Cawa\Html;

use Cawa\Net\Uri;
use Cawa\Renderer\HtmlContainer;

/**
 * @mixin HtmlContainer
 */
trait LinkTrait
{
    /**
     * @return null|string
     */
    public function getHref()
    {
        return $this->getAttribute('href');
    }

    /**
     * @param string $href
     *
     * @return $this|self
     */
    public function setHref(string $href) : self
    {
        $this->addAttribute('href', $href);

        return $this;
    }

    /**
     * @return null|Uri
     */
    public function getUri()
    {
        $href = $this->getAttribute('href');
        if (!$href) {
            return null;
        }

        return new Uri($href);
    }

    /**
     * @param Uri $uri
     *
     * @return Link
     */
    public function setUri(Uri $uri) : self
    {
        return $this->setHref($uri->get());
    }
}
