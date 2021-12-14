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

class Float64Type extends FloatType
{
    /**
     * @var non-empty-string
     */
    private readonly string $format;

    /**
     * @var Endianness
     */
    public readonly Endianness $endianness;

    /**
     * @param Endianness|string $endianness
     */
    public function __construct(
        Endianness|string $endianness = Endianness::DEFAULT,
    ) {
        $this->endianness = Endianness::parse($endianness);

        $this->format = match ($this->endianness) {
            Endianness::BIG => 'E',
            Endianness::LITTLE => 'e',
            default => 'd',
        };

        parent::__construct(8);
    }

    /**
     * @param ReadableStreamInterface $stream
     * @return float
     */
    public function parse(ReadableStreamInterface $stream): float
    {
        $result = \unpack($this->format, $stream->read($this->size));

        return (float)$result[1];
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert(\is_float($data), new \InvalidArgumentException(
            'Expected float type, but ' . \get_debug_type($data) . ' given'
        ));

        return $stream->write(\pack($this->format, $data));
    }
}
