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

namespace Cawa\Html\Tables\ColumnRenderer;

use Cawa\Date\DateTime as DateTimeObject;
use Cawa\Html\Tables\Column;
use Cawa\Intl\TranslatorFactory;

class DateTime extends AbstractRenderer
{
    use TranslatorFactory;

    const DISPLAY_DURATION_FROMSECONDS = 'DURATION_FROMSECONDS';

    /**
     * @var string
     */
    private $format;

    /**
     * @param null $format
     */
    public function __construct($format = null)
    {
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke($content, Column $column, array $primaryValues, array $data) : string
    {
        if (!$content) {
            return '';
        }

        if ($this->format == self::DISPLAY_DURATION_FROMSECONDS) {
            $date = (new DateTimeObject())
                ->addSeconds($content);

            return $date->display(DateTimeObject::DISPLAY_DURATION);
        }

        if (is_string($content)) {
            $content = new DateTimeObject($content);
        }

        if ($this->format == DateTimeObject::DISPLAY_DURATION) {
            return '<abbr title="' . $content->display() . '">' .
                $content->display($this->format) .
                '</abbr>';
        } else {
            return $content->display($this->format);
        }
    }
}
