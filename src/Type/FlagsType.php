<?php

/**
 * This file is part of BitStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

use Serafim\BinStream\Stream\ReadableStreamInterface;
use Serafim\BinStream\Stream\WritableStreamInterface;

/**
 * @template T of \BackedEnum
 * @template-extends Type<T>
 */
class FlagsType extends Type
{
    /**
     * @var IntType
     */
    public readonly IntType $type;

    /**
     * @param class-string<T> $enum
     * @param IntType|class-string<IntType> $type
     */
    public function __construct(
        public readonly string $enum,
        IntType|string $type = new UInt8Type()
    ) {
        $this->type = \is_string($type) ? new $type() : $type;

        parent::__construct($type->size);
    }

    /**
     * @param ReadableStreamInterface $stream
     * @return array<T>
     */
    public function parse(ReadableStreamInterface $stream): array
    {
        $result = [];
        $actual = $this->type->parse($stream);

        foreach (($this->enum)::cases() as $case) {
            if (($actual & $case->value) === $case->value) {
                $result[] = $case;
            }
        }

        return $result;
    }

    /**
     * @param array<T> $data
     * @param WritableStreamInterface $stream
     * @return positive-int|0
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert(\is_array($data) && \array_is_list($data), new \InvalidArgumentException(
            'Expected list type, but ' . \get_debug_type($data) . ' given'
        ));

        $result = 0;

        foreach ($data as $case) {
            $result |= $case->value;
        }

        return $this->type->serialize($result, $stream);
    }
}
