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

/**
 * @template T of mixed
 * @template-implements ArrayTypeInterface<T, list>
 */
class ArrayType implements ArrayTypeInterface
{
    /**
     * @var int
     */
    final public const ARRAY_AUTO_SIZE = -1;

    /**
     * @var TypeInterface
     */
    public readonly TypeInterface $type;

    /**
     * @param TypeInterface<T>|class-string<TypeInterface<T>> $type
     * @param int $count
     */
    public function __construct(
        TypeInterface|string $type = new UInt8Type(),
        public readonly int $count = self::ARRAY_AUTO_SIZE
    ) {
        $this->type = \is_string($type) ? new $type() : $type;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(ReadableStreamInterface $stream): array
    {
        if ($this->count <= 0) {
            return [];
        }

        $result = [];

        for ($i = 0; $i < $this->count; ++$i) {
            /** @psalm-suppress MixedAssignment */
            $result[] = $this->type->parse($stream);
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

        if ($this->count === 0) {
            return 0;
        }

        if ($this->count > 0) {
            $data = \array_slice($data, 0, $this->count);
        }

        $size = 0;

        foreach ($data as $item) {
            $size += $this->type->serialize($item, $stream);
        }

        return $size;
    }
}
