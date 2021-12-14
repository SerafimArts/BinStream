<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Stream;

class WritableResourceStream extends ResourceStream implements WritableStreamInterface
{
    /**
     * {@inheritDoc}
     */
    public function write(string $bytes): int
    {
        \error_clear_last();

        $result = @\fwrite($this->stream, $bytes);

        if ($result === false) {
            throw new \LogicException('Unable to write to stream');
        }

        return $result;
    }
}
