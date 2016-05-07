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

use Cawa\App\HttpFactory;
use Cawa\Controller\ViewController;
use Cawa\Html\Forms\Fields\AbstractField;
use Cawa\Html\Forms\Fields\File;
use Cawa\Html\Forms\Fields\Hidden;
use Cawa\Http\ParameterTrait;
use Cawa\Renderer\HtmlContainer;
use Cawa\Session\SessionFactory;

class Fieldset extends HtmlContainer
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('<fieldset>');
    }

    /**
     * @return array|Fields\AbstractField[]
     */
    public function getFields() : array
    {
        return $this->elements;
    }

}
