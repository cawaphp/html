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

class Button extends AbstractField
{
    const TYPE_SUBMIT = 'submit';
    const TYPE_BUTTON = 'button';
    const TYPE_RESET = 'reset';

    /**
     * @param string $content
     * @param string $label
     */
    public function __construct(string $content, string $label = null)
    {
        parent::__construct('<button>', null, $label);
        $this->getField()->setContent($content);

        $this->getField()->addAttribute('type', self::TYPE_SUBMIT);
    }

    /**
     * @param string $type
     *
     * @return $this|self
     */
    public function setType(string $type) : self
    {
        $this->getField()->addAttribute('type', $type);

        return $this;
    }
}
