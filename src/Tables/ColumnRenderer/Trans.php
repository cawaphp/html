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

namespace Cawa\Html\Tables\ColumnRenderer;

use Cawa\Html\Tables\Column;
use Cawa\Intl\TranslatorFactory;

class Trans extends AbstractRenderer
{
    use TranslatorFactory;

    /**
     * @var string
     */
    private $localeKeys;

    /**
     * @param string $localeKeys
     */
    public function __construct(string $localeKeys)
    {
        $this->localeKeys = $localeKeys;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($content, Column $column, array $primaryValues, array $data) : string
    {
        return self::trans($this->localeKeys . '/' . $content);
    }
}
