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

class Number extends AbstractField
{
    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct('<input />', $name, $label);
        $this->getField()->addAttribute('type', 'number');
        $this->setValidation('int');
    }

    /**
     * @return float
     */
    public function getMin()
    {
        return $this->getField()->getAttribute('min');
    }

    /**
     * @param float $min
     *
     * @return $this|self
     */
    public function setMin(float $min) : self
    {
        $this->getField()->addAttribute('min', (string) $min);

        return $this;
    }

    /**
     * @return float
     */
    public function getMax()
    {
        return $this->getField()->getAttribute('max');
    }

    /**
     * @param float $max
     *
     * @return $this|self
     */
    public function setMax(float $max) : self
    {
        $this->getField()->addAttribute('max', (string) $max);

        return $this;
    }

    /**
     * @return float
     */
    public function getStep()
    {
        return $this->getField()->getAttribute('step');
    }

    /**
     * @param float $step
     *
     * @return $this|self
     */
    public function setStep(float $step) : self
    {
        if ($step >= 1) {
            $this->setValidation('int');
        } else {
            $this->setValidation('float');
        }

        $this->getField()->addAttribute('step', (string) $step);

        return $this;
    }
}
