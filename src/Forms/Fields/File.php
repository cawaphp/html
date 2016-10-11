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

namespace Cawa\Html\Forms\Fields;

use Cawa\Html\Forms\FieldsProperties\MultipleValueInterface;
use Cawa\Html\Forms\Form;

class File extends AbstractField implements MultipleValueInterface
{
    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label = null)
    {
        parent::__construct('<input />', $name, $label);
        $this->getField()->addAttribute('type', 'file');
    }

    /**
     * @var \Cawa\Http\File[]
     */
    private $value;

    /**
     * @return \Cawa\Http\File[]
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param \Cawa\Http\File[] $value
     *
     * @return $this|self|parent
     */
    public function setValue($value) : parent
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $current) {
            if (!$current instanceof \Cawa\Http\File && !is_null($current)) {
                throw new \LogicException(sprintf(
                    "File '%s' value must be an instance of \\Cawa\\Http\\File, '%s' given",
                    $this->getName(),
                    is_object($current) ? get_class($current) : gettype($current)
                ));
            }
        }

        $this->value = $value;

        return $this;
    }

    /**
     * @param Form $form
     */
    public function onAdd(Form $form)
    {
        $form->setMultipart();
    }
}
