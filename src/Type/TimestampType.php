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
 * @template-extends Type<\DateTimeInterface>
 */
class TimestampType extends Type
{
    /**
     * @var IntType
     */
    public readonly IntType $type;

    /**
     * @param IntType|class-string<IntType> $type
     * @param bool $immutable
     */
    public function __construct(
        IntType|string $type = new UInt32Type(),
        private readonly bool $immutable = true,
    ) {
        $this->type = \is_string($type) ? new $type() : $type;

        parent::__construct($this->type->size);
    }

    /**
     * @return \DateTimeInterface
     */
    private function instance(): \DateTimeInterface
    {
        if ($this->immutable) {
            return new \DateTimeImmutable();
        }

        return new \DateTime();
    }

    /**
     * {@inheritDoc}
     */
    public function parse(ReadableStreamInterface $stream): \DateTimeInterface
    {
        $timestamp = $this->type->parse($stream);

        $instance = $this->instance();

        return $instance->setTimestamp($timestamp);
    }


    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert($data instanceof \DateTimeInterface, new \InvalidArgumentException(
            'Expected instance of DateTimeInterface, but ' . \get_debug_type($data) . ' given'
        ));

        return $this->type->serialize($data->getTimestamp(), $stream);
    }
}
