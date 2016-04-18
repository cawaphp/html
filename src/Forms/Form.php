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
use Cawa\App\HttpApp;
use Cawa\Renderer\HtmlContainer;
use Cawa\Controller\ViewController;
use Cawa\Html\Forms\Fields\AbstractField;
use Cawa\Html\Forms\Fields\Hidden;
use Cawa\Http\ParameterTrait;
use Cawa\Session\SessionFactory;

class Form extends HtmlContainer
{
    use HttpFactory;
    use SessionFactory;
    use ParameterTrait;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct('<form>');
        $this->addAttributes([
            'role' => 'form',
            'method' => 'POST',
        ]);
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * @param mixed $name
     *
     * @return $this
     */
    public function setName($name) : self
    {
        return $this->addAttribute('name', $name)->setCsrf(true);
    }

    /**
     * @return null|string
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod(string $method) : self
    {
        return $this->addAttribute('method', $method);
    }

    /**
     * @return null|string
     */
    public function getAction()
    {
        return $this->getAttribute('action');
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction(string $action) : self
    {
        return $this->addAttribute('action', $action);
    }

    /**
     * @return bool
     */
    public function isMultipart() : bool
    {
        return $this->getAttribute('enctype') == 'multipart/form-data';
    }

    /**
     * @param bool $disabled
     *
     * @return $this
     */
    public function setMultipart(bool $multipart = true)
    {
        if ($multipart) {
            return $this->addAttribute('enctype', 'multipart/form-data');
        } else {
            return $this->removeAttribute('enctype');
        }
    }

    /**
     * @var bool
     */
    private $csrf = false;

    /**
     * @return bool
     */
    public function isCsrf()
    {
        return $this->csrf;
    }

    /**
     * @param bool $csrf
     *
     * @return $this
     */
    public function setCsrf(bool $csrf) : self
    {
        $this->csrf = $csrf;

        return $this;
    }

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getValue(string $name)
    {
        if (!isset($this->values[$name])) {
            return null;
        }

        $return = $this->values[$name]['value'] ?? null;
        if ($return == '') {
            $return = null;
        }

        return $return;
    }

    /**
     * @param ViewController $element
     *
     * @return $this
     */
    public function add(ViewController $element)
    {
        $this->populateValue($element);

        return parent::add($element);
    }

    /**
     * @param ViewController $element
     *
     * @return $this
     */
    public function addFirst(ViewController $element)
    {
        $this->populateValue($element);

        return parent::addFirst($element);
    }

    /**
     * @param AbstractField|ViewController $element
     *
     * @return bool
     */
    protected function populateValue(AbstractField $element) : bool
    {
        if (!$this->isSubmit()) {
            return false;
        }

        if (!$name = $element->getName()) {
            return false;
        }

        $userInput = $this->request()->getArg($element->getName());

        if (is_null($userInput)) {
            return false;
        }

        $element->setValue($userInput);

        $value = [
            'userInput' => $userInput,
            'valid' => true,
            'value' => null,
        ];

        if ($element->getPrimitiveType()) {
            $typeReturn = $this->validateType($userInput, $element->getPrimitiveType());
            if (is_null($typeReturn)) {
                $value['valid'] = false;
            } else {
                $value['value'] = $typeReturn;
            }
        } else {
            $value['value'] = $userInput;
        }

        if (method_exists($element, 'isValid')) {
            $value['valid'] = $element->isValid();
        }

        if (!$value['valid']) {
            unset($value['value']);
        }

        $this->values[$name] = $value;

        return $value['valid'];
    }

    /**
     * @return bool
     */
    public function isSubmit() : bool
    {
        if ($this->request()->getMethod() != $this->getMethod()) {
            return false;
        }

        if ($this->csrf) {
            $csrfName = 'CSRF_' . $this->getName();
            $csrfToken = self::session()->get($csrfName);
            if ($this->request()->getArg('_csrf', 'string') != $csrfToken) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        if (sizeof($this->values) == 0) {
            return false;
        }

        foreach ($this->values as $name => $value) {
            if ($value['valid'] === false) {
                return false;
            }
        }

        return true;
    }

    public function render()
    {
        if ($this->csrf) {
            if (!$this->getName()) {
                throw new \LogicException("Can't set CSRF token with form name");
            }

            $csrfName = 'CSRF_' . $this->getName();
            $csrfToken = md5((string) mt_rand());
            self::session()->set($csrfName, $csrfToken);
            $this->addFirst(new Hidden('_csrf', $csrfToken));
        }

        $return = parent::render();

        return $return;
    }
}
