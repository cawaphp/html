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

namespace Cawa\Html\Tables;

use Cawa\Html\Tables\ColumnRenderer\AbstractRenderer;
use Cawa\Renderer\HtmlContainer;

class Column extends HtmlContainer
{
    /**
     * @param string $id
     * @param string $name
     */
    public function __construct(string $id, $name = null)
    {
        parent::__construct('<th>');
        $this->id = $id;
        if ($name) {
            $this->setContent($name);
        }
    }

    /**
     * @var string
     */
    private $id;

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this|self|parent
     */
    public function setId(string $id) : parent
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->getContent();
    }

    /**
     * @param string $name
     *
     * @return $this|self
     */
    public function setName(string $name) : self
    {
        return $this->setContent($name);
    }

    /**
     * @var bool
     */
    private $primary = false;

    /**
     * @return bool
     */
    public function isPrimary() : bool
    {
        return $this->primary;
    }

    /**
     * @param bool $primary
     *
     * @return $this|self
     */
    public function setPrimary(bool $primary = true) : self
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * @var bool
     */
    private $hideable = true;

    /**
     * @return bool
     */
    public function isHideable() : bool
    {
        return $this->hideable;
    }

    /**
     * @param bool $hideable
     *
     * @return $this|self
     */
    public function setHideable(bool $hideable = false) : self
    {
        $this->hideable = $hideable;

        return $this;
    }

    /**
     * @var bool
     */
    private $visible = true;

    /**
     * @return bool
     */
    public function isVisible() : bool
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     *
     * @return $this|self
     */
    public function setVisible(bool $visible = false) : self
    {
        if ($this->hideable == false && $visible == false) {
            throw new \LogicException(sprintf("Can't set visibility hidden on cols '%s'", $this->getId()));
        }

        $this->visible = $visible;

        return $this;
    }

    /**
     * @var callable[]|AbstractRenderer[]
     */
    private $renderer = [];

    /**
     * @return callable[]|AbstractRenderer[]
     */
    public function getRenderer() : array
    {
        return $this->renderer;
    }

    /**
     * @param callable|AbstractRenderer $renderer
     *
     * @return $this|self
     */
    public function addRenderer($renderer) : self
    {
        $this->renderer[] = $renderer;

        return $this;
    }

    /**
     * @param callable[]|AbstractRenderer[] $renderer
     *
     * @return $this|self
     */
    public function setRenderer(array $renderer) : self
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if ($this->visible) {
            return parent::render();
        } else {
            return '';
        }
    }
}
