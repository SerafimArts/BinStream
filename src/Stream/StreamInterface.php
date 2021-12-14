<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Stream;

interface StreamInterface
{
    /**
     * Seeks to a position.
     *
     * @link https://php.net/manual/en/seekableiterator.seek.php
     *
     * @param int $offset The position to seek to.
     * @param Seek $seek The position's seek mode.
     * @return int
     */
    public function seek(int $offset, Seek $seek = Seek::SET): int;

    /**
     * Returns current stream position in bytes.
     *
     * @return positive-int|0
     */
    public function offset(): int;
}
