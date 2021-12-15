<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream;

use Serafim\BinStream\Stream\ReadableResourceStream;
use Serafim\BinStream\Stream\ReadableStreamInterface;
use Serafim\BinStream\Type\ArrayType;
use Serafim\BinStream\Type\BitMaskType;
use Serafim\BinStream\Type\CharType;
use Serafim\BinStream\Type\Endianness;
use Serafim\BinStream\Type\EnumType;
use Serafim\BinStream\Type\FlagsType;
use Serafim\BinStream\Type\Float32Type;
use Serafim\BinStream\Type\Float64Type;
use Serafim\BinStream\Type\Int16Type;
use Serafim\BinStream\Type\Int32Type;
use Serafim\BinStream\Type\Int64Type;
use Serafim\BinStream\Type\Int8Type;
use Serafim\BinStream\Type\IntType;
use Serafim\BinStream\Type\IntTypeInterface;
use Serafim\BinStream\Type\Repository;
use Serafim\BinStream\Type\StringType;
use Serafim\BinStream\Type\StringTypeInterface;
use Serafim\BinStream\Type\TimestampType;
use Serafim\BinStream\Type\TypeInterface;
use Serafim\BinStream\Type\UInt16Type;
use Serafim\BinStream\Type\UInt32Type;
use Serafim\BinStream\Type\UInt64Type;
use Serafim\BinStream\Type\UInt8Type;

/**
 * @template-extends Stream<ReadableStreamInterface>
 */
final class Reader extends Stream implements ReadableStreamInterface
{
    /**
     * @param ReadableStreamInterface $stream
     * @param Repository $repository
     */
    public function __construct(
        ReadableStreamInterface $stream,
        Repository $repository = new Repository(),
    ) {
        parent::__construct($stream, $repository);
    }

    /**
     * @param non-empty-string $pathname
     * @param Repository $repository
     * @return self
     */
    public static function fromPathname(
        string $pathname,
        Repository $repository = new Repository()
    ): self {
        $stream = new ReadableResourceStream(\fopen($pathname, 'rb'));

        return new self($stream, $repository);
    }

    /**
     * @return int
     * @throws \Throwable
     */
    public function int8(): int
    {
        return $this->readAs(Int8Type::class);
    }

    /**
     * @template TReturn of mixed
     *
     * @param class-string<TypeInterface<TReturn>>|TypeInterface<TReturn> $type
     * @return TReturn
     * @throws \Throwable
     * @psalm-suppress MixedInferredReturnType
     */
    public function readAs(string|TypeInterface $type): mixed
    {
        if (\is_string($type)) {
            $type = $this->types->get($type);
        }

        return $type->parse($this);
    }

    /**
     * @return positive-int|0
     * @throws \Throwable
     */
    public function uint8(): int
    {
        return $this->readAs(UInt8Type::class);
    }

    /**
     * @return int
     * @throws \Throwable
     */
    public function int16(): int
    {
        return $this->readAs(Int16Type::class);
    }

    /**
     * @param Endianness|string $endianness
     * @return positive-int|0
     * @throws \Throwable
     */
    public function uint16(Endianness|string $endianness = Endianness::DEFAULT): int
    {
        return $this->readAs(new UInt16Type($endianness));
    }

    /**
     * @return int
     * @throws \Throwable
     */
    public function int32(): int
    {
        return $this->readAs(Int32Type::class);
    }

    /**
     * @param Endianness $endianness
     * @return positive-int|0
     * @throws \Throwable
     */
    public function uint32(Endianness $endianness = Endianness::DEFAULT): int
    {
        return $this->readAs(new UInt32Type($endianness));
    }

    /**
     * @return int
     * @throws \Throwable
     */
    public function int64(): int
    {
        return $this->readAs(Int64Type::class);
    }

    /**
     * @param Endianness $endianness
     * @return positive-int|0
     * @throws \Throwable
     */
    public function uint64(Endianness $endianness = Endianness::DEFAULT): int
    {
        return $this->readAs(new UInt64Type($endianness));
    }

    /**
     * @param Endianness $endianness
     * @return float
     * @throws \Throwable
     */
    public function float32(Endianness $endianness = Endianness::DEFAULT): float
    {
        return $this->readAs(new Float32Type($endianness));
    }

    /**
     * @param Endianness $endianness
     * @return float
     * @throws \Throwable
     */
    public function float64(Endianness $endianness = Endianness::DEFAULT): float
    {
        return $this->readAs(new Float64Type($endianness));
    }

    /**
     * @return non-empty-string
     * @throws \Throwable
     */
    public function char(): string
    {
        return $this->readAs(CharType::class);
    }

    /**
     * @param int $size
     * @return string
     * @throws \Throwable
     */
    public function string(int $size = StringType::STRING_AUTO_SIZE): string
    {
        if ($size === 0) {
            return '';
        }

        return $this->readAs(new StringType($size));
    }

    /**
     * @param IntTypeInterface|class-string<IntTypeInterface> $type
     * @param bool $immutable
     * @return \DateTimeInterface
     * @throws \Throwable
     */
    public function timestamp(
        IntTypeInterface|string $type = new UInt32Type(),
        bool $immutable = true
    ): \DateTimeInterface {
        return $this->readAs(new TimestampType($type, $immutable));
    }

    /**
     * @template T of \BackedEnum
     *
     * @param class-string<T> $enum
     * @param IntTypeInterface|StringTypeInterface|class-string<IntTypeInterface>|class-string<StringTypeInterface> $type
     * @return T
     * @throws \Throwable
     */
    public function enum(string $enum, IntTypeInterface|StringTypeInterface|string $type = new UInt32Type()): \BackedEnum
    {
        return $this->readAs(new EnumType($enum, $type));
    }

    /**
     * @template T of \BackedEnum
     *
     * @param class-string<T> $enum
     * @param IntTypeInterface|class-string<IntTypeInterface> $type
     * @return list<T>
     * @throws \Throwable
     */
    public function flags(string $enum, IntTypeInterface|string $type): array
    {
        return $this->readAs(new FlagsType($enum, $type));
    }

    /**
     * @template T of mixed
     * @param TypeInterface<T>|class-string<TypeInterface<T>> $type
     * @param positive-int $count
     * @return list<T>
     * @throws \Throwable
     */
    public function array(TypeInterface|string $type, int $count = 1): array
    {
        return $this->readAs(new ArrayType($type, $count));
    }

    /**
     * @param positive-int $count
     * @return list<bool>
     * @throws \Throwable
     */
    public function bitmask(int $count = 1): array
    {
        return $this->readAs(new BitMaskType($count));
    }

    /**
     * @param positive-int|0 $bytes
     * @return self
     */
    public function slice(int $bytes): self
    {
        $stream = \fopen('php://memory', 'ab+');
        \fwrite($stream, $this->read($bytes));
        \rewind($stream);

        return new self(new ReadableResourceStream($stream, true));
    }

    /**
     * {@inheritDoc}
     */
    public function read(int $bytes): string
    {
        return $this->stream->read($bytes);
    }
}
