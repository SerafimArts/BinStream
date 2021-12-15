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
 * @template-implements ArrayTypeInterface<T, list>
 */
class FlagsType implements ArrayTypeInterface
{
    /**
     * @var IntTypeInterface
     */
    public readonly IntTypeInterface $type;

    /**
     * @param class-string<T> $enum
     * @param IntTypeInterface|class-string<IntTypeInterface> $type
     */
    public function __construct(
        public readonly string $enum,
        IntTypeInterface|string $type = new UInt8Type()
    ) {
        $this->type = \is_string($type) ? new $type() : $type;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(ReadableStreamInterface $stream): array
    {
        $result = [];
        $actual = $this->type->parse($stream);

        /** @var \BackedEnum $case */
        foreach (($this->enum)::cases() as $case) {
            if (($actual & $case->value) === $case->value) {
                $result[] = $case;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
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
