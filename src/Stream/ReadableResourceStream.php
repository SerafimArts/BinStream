<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Stream;

class ReadableResourceStream extends ResourceStream implements ReadableStreamInterface
{
    /**
     * {@inheritDoc}
     */
    public function read(int $bytes): string
    {
        assert($bytes > 0, new \InvalidArgumentException(
            'Bytes size must be greater than 0'
        ));

        $result = \fread($this->stream, $bytes);

        return $result . \str_repeat("\x00", $bytes - \strlen($result));
    }
}
