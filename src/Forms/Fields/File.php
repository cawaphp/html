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

namespace Cawa\Html\Forms\Fields;

use Cawa\Html\Forms\Form;

class File extends AbstractField
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
     * @var \Cawa\Http\File
     */
    private $value;

    /**
     * @return \Cawa\Http\File
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param \Cawa\Http\File $value
     *
     * @return File
     */
    public function setValue($value) : parent
    {
        if (!$value instanceof \Cawa\Http\File) {
            throw new \LogicException("File '%s' value must be an instance of \\Cawa\\Http\\File", $this->getName());
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
