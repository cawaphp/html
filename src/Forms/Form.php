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
use Cawa\Html\Forms\FieldsProperties\MultipleValueInterface;
use Cawa\Http\ParameterTrait;
use Cawa\Net\Uri;
use Cawa\Renderer\HtmlContainer;
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
        if ($this->getMethod() != 'GET') {
            $this->setCsrf(true);
        }

        return $this->addAttribute('name', $name);
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
        if ($method == 'GET') {
            $this->setCsrf(false);
        }

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
     * @var array
     */
    protected $valuesAsArray = [];

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getValue(string $name)
    {
        if (isset($this->valuesAsArray[$name])) {
            return $this->valuesAsArray[$name];
        }

        if (!array_key_exists($name, $this->values)) {
            return null;
        }

        $return = $this->values[$name]['value'] ?? null;

        if ($return === '') {
            $return = null;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ViewController ...$elements)
    {
        foreach ($elements as $element) {
            if (method_exists($element, 'onAdd')) {
                $element->onAdd($this);
            }

            $this->populateValue($element);
        }

        return parent::add(...$elements);
    }

    /**
     * {@inheritdoc}
     */
    public function addFirst(ViewController ...$elements)
    {
        foreach ($elements as $element) {
            if (method_exists($element, 'onAdd')) {
                $element->onAdd($this);
            }

            $this->populateValue($element);
        }

        return parent::addFirst(...$elements);
    }

    /**
     * @var array
     */
    private $arrayNameIndex = [];

    /**
     * @param AbstractField|Group|Fieldset $field
     *
     * @return bool
     */
    protected function populateValue($field) : bool
    {
        if (!$this->isSubmit()) {
            return false;
        }

        if ($field instanceof Group || $field instanceof Fieldset) {
            $elements = $field->getFields();
        } elseif ($field instanceof AbstractField) {
            $elements = [$field];
        } else {
            throw new \InvalidArgumentException(sprintf(
                "Invalid type '%s'",
                is_object($field) ? get_class($field) : gettype($field)
            ));
        }

        $return = true;

        foreach ($elements as $element) {
            if ($element instanceof AbstractField) {
                $return = !$this->getFieldValue($element) ? false : $return;
            } else {
                $return = !$this->populateValue($element) ? false : $return;
            }
        }

        return $return;
    }

    /**
     * @param AbstractField|Group|Fieldset $element
     *
     * @return bool
     */
    private function getFieldValue($element)
    {
        if (!$name = $element->getName()) {
            return false;
        }

        // index name array
        /*
        if (substr($name, -2) == '[]' && !$element instanceof MultipleValueInterface) {
            $currentName = substr($name, 0, -2);
            if (!isset($this->arrayNameIndex[$currentName])) {
                $this->arrayNameIndex[$currentName] = 0;
            } else {
                $this->arrayNameIndex[$currentName]++;
            }

            $name = $currentName . '[' . $this->arrayNameIndex[$currentName] . ']';
        }
        */

        if ($element instanceof File) {
            $userInput = $this->request()->getUploadedFile($name);
        } else {
            $userInput = $this->request()->getArg($name);
        }

        $userInput = $userInput != '' ? $userInput : null;

        $value = [
            'userInput' => $userInput,
            'valid' => true,
            'value' => null,
        ];

        if ($element->isRequired() && is_null($userInput)) {
            $value['valid'] = false;
        }

        if ($element->getPrimitiveType() && !is_null($userInput)) {
            $typeReturn = $this->validateType($userInput, $element->getPrimitiveType());
            if (is_null($typeReturn)) {
                $value['valid'] = false;
            } else {
                $value['value'] = $typeReturn;
            }
        } else {
            $value['value'] = $userInput;
        }

        if (method_exists($element, 'isValid') && $value['valid'] == true && $element->getValue()) {
            $value['valid'] = $element->isValid();
        }

        if (!$value['valid']) {
            unset($value['value']);
        }

        // We set values with type cast value if valid, else type cast, else user input
        $elementValue = $value['value'] ?? (isset($typeReturn) ? $typeReturn : $userInput);

        if (is_array($elementValue) && !$element instanceof MultipleValueInterface) {
            $elementValue = null;
        }

        $element->setValue($elementValue);


        // !$element instanceof MultipleValueInterface

        $this->values[$name] = $value;

        // store array in friendly property
        if (stripos($name, '[') !== false && isset($value['value']) && $value['value'] != '') {
            $names = explode('[', str_replace(']', '', $name));
            if ($names[sizeof($names) -1] == '') {
                array_pop($names);
            }

            $valueAsArray = [];
            $ref = &$valueAsArray;
            $leave = false;

            while ($leave == false) {
                $key = array_shift($names);

                if (is_null($key)) {
                    $leave = true;
                    $ref = $value['value'];
                } else {
                    $ref = &$ref[$key];
                }
            }

            $this->valuesAsArray = array_replace_recursive($this->valuesAsArray, $valueAsArray);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isSubmit() : bool
    {
        if ($this->getMethod() == 'GET' && !$this->getName()) {
            throw new \LogicException("Can't test isSubmit without form name on method GET");
        }

        if ($this->request()->getMethod() != $this->getMethod()) {
            return false;
        }

        if ($this->getMethod() == 'GET' && !$this->request()->getQuery($this->getName(), 'bool')) {
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
            if (isset($value['valid']) && $value['valid'] === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * add Csrf input hidden
     */
    private function addCsrf()
    {
        if (!$this->getName()) {
            throw new \LogicException("Can't set CSRF token with form name");
        }

        $csrfName = 'CSRF_' . $this->getName();
        $csrfToken = md5((string) mt_rand());
        self::session()->set($csrfName, $csrfToken);
        $this->addFirst(new Hidden('_csrf', $csrfToken));
    }

    /**
     * @return array
     */
    public function export() : array
    {
        if ($this->csrf) {
            $this->addCsrf();
        }

        $return = [
            'form' => $this->renderOuter(),
            'elements' => [],
        ];

        /** @var AbstractField $element */
        foreach ($this->elements as $element) {
            if (!$element->getName()) {
                throw new \InvalidArgumentException(sprintf(
                    "Can't export fields '%s' without name",
                    get_class($element)
                ));
            }

            $item = [
                'name' => $element->getName(),
                'outer' => $element->renderOuter(),
                'value' => $element->getValue(),
            ];

            $item['field'] = $element->getField()->render();
            if ($element->getLabel()) {
                $item['label'] = $element->getLabel()->render();
                $item['labelOuter'] = $element->getLabel()->renderOuter();
                $item['labelContent'] = $element->getLabel()->getContent();
            }

            $return['elements'][$element->getName()] = $item;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->alterBeforeRender();

        return parent::render();
    }

    /**
     * {@inheritdoc}
     */
    public function renderOuter() : array
    {
        $this->alterBeforeRender();

        return parent::renderOuter();
    }

    /**
     *
     */
    protected function alterBeforeRender()
    {
        if ($this->csrf) {
            $this->addCsrf();
        }

        // append all querystring && input hidden for submit
        if ($this->getMethod() == 'GET') {
            $this->add(new Hidden($this->getName(), '1'));

            /*
            $uri = new Uri($this->getAction());
            if ($uri->getQueries()) {
                foreach ($uri->getQueries() as $key => $value) {
                    if (isset($this->values[$key]) || isset($this->values[$key . '[]'])) {
                        continue;
                    }

                    if (!is_array($value)) {
                        $this->add(new Hidden($key, $value));
                    } else {
                        foreach ($value as $current) {
                            $this->add(new Hidden($key . '[]', $current));
                        }
                    }

                    $this->setAction($uri->removeAllQueries()->get());
                }
            }
            */
        }
    }
}
