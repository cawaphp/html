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

namespace Cawa\Html\Forms\Fields;

class Plain extends AbstractField
{
    /**
     * @param string $label
     * @param string $content
     */
    public function __construct(string $label, string $content = null)
    {
        parent::__construct('<div>', null, $label);

        if ($content) {
            $this->setContent($content);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPlaceholder(string $name = null) : parent
    {
        return $this;
    }
}
