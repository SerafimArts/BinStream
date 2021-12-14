<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

use Serafim\BinStream\Stream\ReadableStreamInterface;
use Serafim\BinStream\Stream\WritableStreamInterface;

class Int32Type extends IntType
{
    /**
     * Int32Type constructor.
     */
    public function __construct()
    {
        parent::__construct(4);
    }

    /**
     * {@inheritDoc}
     */
    public function parse(ReadableStreamInterface $stream): int
    {
        $result = \unpack('l', $stream->read($this->size));

        return (int)$result[1];
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert(\is_int($data), new \InvalidArgumentException(
            'Expected int type, but ' . \get_debug_type($data) . ' given'
        ));

        return $stream->write(\pack('l', $data));
    }
}
