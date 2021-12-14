<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Stream;

interface WritableStreamInterface extends StreamInterface
{
    /**
     * Writes several bytes into stream.
     *
     * @param non-empty-string $bytes
     * @return positive-int
     */
    public function write(string $bytes): int;
}
