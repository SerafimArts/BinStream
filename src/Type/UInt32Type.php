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

class UInt32Type extends UIntType
{
    /**
     * @var non-empty-string
     */
    private readonly string $format;

    /**
     * @param Endianness|string $endianness
     */
    public function __construct(Endianness|string $endianness = Endianness::DEFAULT)
    {
        parent::__construct(4, $endianness);

        $this->format = match ($this->endianness) {
            Endianness::BIG => 'N',
            Endianness::LITTLE => 'V',
            default => 'L',
        };
    }

    /**
     * {@inheritDoc}
     */
    public function parse(ReadableStreamInterface $stream): int
    {
        $result = \unpack($this->format, $stream->read($this->size));

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

        return $stream->write(\pack($this->format, $data));
    }
}
