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

class StringType implements StringTypeInterface
{
    /**
     * @var int
     */
    final public const STRING_AUTO_SIZE = -1;

    /**
     * @param int $size
     */
    public function __construct(
        public readonly int $size = self::STRING_AUTO_SIZE
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function parse(ReadableStreamInterface $stream): string
    {
        if ($this->size <= self::STRING_AUTO_SIZE) {
            $buffer = '';

            while (($char = $stream->read(1)) !== "\x00") {
                $buffer .= $char;
            }

            return $buffer;
        }

        return \rtrim($stream->read($this->size), "\x00");
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert(\is_string($data), new \InvalidArgumentException(
            'Expected string type, but ' . \get_debug_type($data) . ' given'
        ));

        if ($this->size > self::STRING_AUTO_SIZE) {
            $data = \str_pad($data, $this->size, "\x00");
        }

        return $stream->write($data);
    }
}
