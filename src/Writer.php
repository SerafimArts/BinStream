<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream;

use Serafim\BinStream\Stream\WritableResourceStream;
use Serafim\BinStream\Stream\WritableStreamInterface;
use Serafim\BinStream\Type\ArrayType;
use Serafim\BinStream\Type\BitMaskType;
use Serafim\BinStream\Type\CharType;
use Serafim\BinStream\Type\Endianness;
use Serafim\BinStream\Type\EnumType;
use Serafim\BinStream\Type\Float32Type;
use Serafim\BinStream\Type\Float64Type;
use Serafim\BinStream\Type\Int16Type;
use Serafim\BinStream\Type\Int32Type;
use Serafim\BinStream\Type\Int64Type;
use Serafim\BinStream\Type\Int8Type;
use Serafim\BinStream\Type\IntType;
use Serafim\BinStream\Type\Repository;
use Serafim\BinStream\Type\StringType;
use Serafim\BinStream\Type\TimestampType;
use Serafim\BinStream\Type\TypeInterface;
use Serafim\BinStream\Type\UInt16Type;
use Serafim\BinStream\Type\UInt32Type;
use Serafim\BinStream\Type\UInt64Type;
use Serafim\BinStream\Type\UInt8Type;

/**
 * @template-extends Stream<WritableStreamInterface>
 */
final class Writer extends Stream implements WritableStreamInterface
{
    /**
     * @param WritableStreamInterface $stream
     * @param Repository $repository
     */
    public function __construct(
        WritableStreamInterface $stream,
        Repository $repository = new Repository(),
    ) {
        parent::__construct($stream, $repository);
    }

    /**
     * @param non-empty-string $pathname
     * @param bool $flush
     * @param Repository $repository
     * @return self
     */
    public static function fromPathname(
        string $pathname,
        bool $flush = true,
        Repository $repository = new Repository()
    ): self {
        $stream = new WritableResourceStream(\fopen($pathname, $flush ? 'wb+' : 'rb+'));

        return new self($stream, $repository);
    }

    /**
     * @param int $char
     * @return positive-int
     * @throws \Throwable
     */
    public function int8(int $char): int
    {
        return $this->writeAs($char, Int8Type::class);
    }

    /**
     * @param string|TypeInterface $type
     * @param mixed $data
     * @return positive-int
     * @throws \Throwable
     */
    public function writeAs(mixed $data, string|TypeInterface $type): int
    {
        if (\is_string($type)) {
            $type = $this->types->get($type);
        }

        return $type->serialize($data, $this);
    }

    /**
     * @param positive-int|0 $data
     * @return positive-int
     * @throws \Throwable
     */
    public function uint8(int $uchar): int
    {
        return $this->writeAs($uchar, UInt8Type::class);
    }

    /**
     * @param int $short
     * @return positive-int
     * @throws \Throwable
     */
    public function int16(int $short): int
    {
        return $this->writeAs($short, Int16Type::class);
    }

    /**
     * @param positive-int|0 $ushort
     * @param Endianness|string $endianness
     * @return positive-int
     * @throws \Throwable
     */
    public function uint16(int $ushort, Endianness|string $endianness = Endianness::DEFAULT): int
    {
        return $this->writeAs($ushort, new UInt16Type($endianness));
    }

    /**
     * @param int $int
     * @return positive-int|0
     * @throws \Throwable
     */
    public function int32(int $int): int
    {
        return $this->writeAs($int, Int32Type::class);
    }

    /**
     * @param positive-int|0 $uint
     * @param Endianness $endianness
     * @return positive-int
     * @throws \Throwable
     */
    public function uint32(int $uint, Endianness $endianness = Endianness::DEFAULT): int
    {
        return $this->writeAs($uint, new UInt32Type($endianness));
    }

    /**
     * @param int $long
     * @return positive-int
     * @throws \Throwable
     */
    public function int64(int $long): int
    {
        return $this->writeAs($long, Int64Type::class);
    }

    /**
     * @param positive-int|0 $ulong
     * @param Endianness $endianness
     * @return positive-int
     * @throws \Throwable
     */
    public function uint64(int $ulong, Endianness $endianness = Endianness::DEFAULT): int
    {
        return $this->writeAs($ulong, new UInt64Type($endianness));
    }

    /**
     * @param float $float
     * @param Endianness $endianness
     * @return positive-int
     * @throws \Throwable
     */
    public function float32(float $float, Endianness $endianness = Endianness::DEFAULT): int
    {
        return $this->writeAs($float, new Float32Type($endianness));
    }

    /**
     * @param float $double
     * @param Endianness $endianness
     * @return positive-int
     * @throws \Throwable
     */
    public function float64(float $double, Endianness $endianness = Endianness::DEFAULT): int
    {
        return $this->writeAs($double, new Float64Type($endianness));
    }

    /**
     * @param non-empty-string $char
     * @return positive-int
     * @throws \Throwable
     */
    public function char(string $char): int
    {
        return $this->writeAs($char ?: "\x00", CharType::class);
    }

    /**
     * @param string $string
     * @param int $size
     * @return positive-int
     * @throws \Throwable
     */
    public function string(string $string, int $size = StringType::STRING_AUTO_SIZE): int
    {
        return $this->writeAs($string, new StringType($size));
    }

    /**
     * @param \DateTimeInterface $date
     * @param IntType|class-string<IntType> $type
     * @return positive-int
     * @throws \Throwable
     */
    public function timestamp(\DateTimeInterface $date, IntType|string $type = new UInt32Type()): int
    {
        return $this->writeAs($date, new TimestampType($type));
    }

    /**
     * @param \BackedEnum $case
     * @param IntType|class-string<IntType> $type
     * @return positive-int
     * @throws \Throwable
     */
    public function enum(\BackedEnum $case, IntType|string $type = new UInt32Type()): int
    {
        return $this->writeAs($case, new EnumType($case::class, $type));
    }

    /**
     * @template T of mixed
     * @param array<T> $array
     * @param TypeInterface<T>|class-string<TypeInterface<T>> $type
     * @return positive-int
     * @throws \Throwable
     */
    public function array(array $array, TypeInterface|string $type): int
    {
        return $this->writeAs($array, new ArrayType($type, \count($array)));
    }

    /**
     * @param array<bool> $mask
     * @return positive-int
     * @throws \Throwable
     */
    public function bitmask(array $mask): int
    {
        return $this->writeAs($mask, new BitMaskType(\count($mask)));
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $bytes): int
    {
        return $this->stream->write($bytes);
    }
}
