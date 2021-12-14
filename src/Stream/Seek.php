<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Stream;

enum Seek
{
    /**
     * Set position equal to offset bytes.
     */
    case SET;

    /**
     * Set position to current location plus offset.
     */
    case CURSOR;
}
