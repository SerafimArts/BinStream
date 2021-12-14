<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Stream;

interface ReadableStreamInterface extends StreamInterface
{
    /**
     * Read bytes from stream.
     *
     * @param positive-int $bytes
     * @return non-empty-string
     */
    public function read(int $bytes): string;
}
