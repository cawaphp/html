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

use Cawa\Date\Time;
use Cawa\Html\Tables\Column;
use Cawa\Intl\TranslatorFactory;

class Duration extends AbstractRenderer
{
    use TranslatorFactory;

    /**
     * {@inheritdoc}
     */
    public function __invoke($content, Column $column, array $primaryValues, array $data) : string
    {
        if (!$content) {
            return '';
        }

        if (is_string($content)) {
            $content = new Time($content);
        } elseif (is_int($content)) {
            $content = Time::createFromTime(0, 0, 0)->addSeconds($content);
        }

        return $content->display(Time::DISPLAY_DURATION);
    }
}
