# Bin Stream

Binary stream reader and writer.

## Installation

`composer require serafim/bin-stream`

[Package on packagist.org](https://packagist.org/packages/serafim/bin-stream)

## Introduction

Bin stream package provides the ability to read and write binary sources.

## Basic API

Any stream has several common methods. This is `offset(): int` to get the
current position of the cursor and `seek(int, Seek): int` that indicating the
position of the cursor.

In addition, the `Serafim\BinStream\Reader` class provides a `read(int): string`
method, and the `Serafim\BinStream\Writer` provides a `write(string): int` method.

```php
use Serafim\BinStream\Reader;
use Serafim\BinStream\Writer;
use Serafim\BinStream\Stream\Seek;

$reader = Reader::fromPathname(__DIR__ . '/path/to/file.bin');
$writer = Writer::fromPathname(__DIR__ . '/path/to/file.bin');

// Raw API
$reader->seek(23);                  // Seek to offset 23
$writer->seek(23);                  //

$reader->seek(23, Seek::CURSOR);    // Seek to offset plus 23 (current + 23)
$writer->seek(23, Seek::CURSOR);    //

$reader->offset();                  // Returns current offset: int(46)
$writer->offset();                  //

// Read and Write
$reader->read(42);                  // Returns reading bytes: string(42) "..."
$writer->write('abcdef');           // Returns writing size: int(6)
```

## Type System

Each class contains methods for converting data from binary. In the case of
a reader, this is `readAs(string|TypeInterface): mixed`, and in the case of a
writer, `writeAs(mixed, string|TypeInterface): int`.

For example:
```php
use Serafim\BinStream\Reader;
use Serafim\BinStream\Writer;
use Serafim\BinStream\Type\UInt32Type;
use Serafim\BinStream\Type\Endianness;

$reader = Reader::fromPathname(__DIR__ . '/path/to/source.bin');
$writer = Writer::fromPathname(__DIR__ . '/path/to/target.bin');

// Read data as uint32le
$data = $reader->readAs(new UInt32Type());
// OR reference to the class
$data = $reader->readAs(UInt32Type::class);

// Write data as uint32be
$writer->writeAs($data, new UInt32Type(endianness: Endianness::BIG));
```

### Type Aliases

For each type, you can specify an alias to simplify its use.

```php
use Serafim\BinStream\Reader;
use Serafim\BinStream\Writer;
use Serafim\BinStream\Type\Repository;

$types = new Repository();
$types->alias(\Path\To\Class\PlatformAwareInt::class, 'int');
$types->alias(\Path\To\Class\PlatformAwareUnsignedInt::class, 'uint');

$reader = Reader::fromPathname(__DIR__ . '/path/to/source.bin', $types);
$writer = Writer::fromPathname(__DIR__ . '/path/to/target.bin', $types);

// Usage

$data = $reader->readAs('int');
$writer->writeAs($data, 'uint');
```

#### Predefined Type Aliases

| PHP Type                               | Aliases                   |
|----------------------------------------|---------------------------|
| `Serafim\BinStream\Type\ArrayType`     | `array`                   |
| `Serafim\BinStream\Type\BitMaskType`   | `bitmask`                 |
| `Serafim\BinStream\Type\CharType`      | `char`                    |
| `Serafim\BinStream\Type\StringType`    | `string`                  |
| `Serafim\BinStream\Type\EnumType`      | `enum`                    |
| `Serafim\BinStream\Type\FlagsType`     | `flags`                   |
| `Serafim\BinStream\Type\TimestampType` | `timestamp` and `date`    |
| `Serafim\BinStream\Type\Float32Type`   | `float32` and `float`     |
| `Serafim\BinStream\Type\Float64Type`   | `float64` and `double`    |
| `Serafim\BinStream\Type\Int8Type`      | `int8`                    |
| `Serafim\BinStream\Type\Int16Type`     | `int16`                   |
| `Serafim\BinStream\Type\Int32Type`     | `int32`                   |
| `Serafim\BinStream\Type\Int64Type`     | `int64`                   |
| `Serafim\BinStream\Type\UInt8Type`     | `uint8`                   |
| `Serafim\BinStream\Type\UInt16Type`    | `uint16`                  |
| `Serafim\BinStream\Type\UInt32Type`    | `uint32`                  |
| `Serafim\BinStream\Type\UInt64Type`    | `uint64`                  |

### Custom Types

```php
use Serafim\BinStream\Type\TypeInterface;
use Serafim\BinStream\Stream\ReadableStreamInterface;
use Serafim\BinStream\Stream\WritableStreamInterface;

/**
 * @template-implements TypeInterface<int>
 */
class ExampleInt8Type implements TypeInterface
{
    public function parse(ReadableStreamInterface $stream): string
    {
        return ord($stream->read(1));
    }

    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert(is_int($data), new InvalidArgumentException('Can not write non-int type'));

        return $stream->write(chr($data));
    }
}
```

## DSL

In typed `readAs` and `writeAs` methods, you can use DSL which contains the
following format: `TYPE_NAME<CONSTRUCTOR_ARGUMENT_1, CONSTRUCTOR_ARGUMENT_2>`.

For example `array<int8, 42>` is equals to `new ArrayType(new Int8Type(), 42)`.

```php
use Serafim\BinStream\Reader;
use Serafim\BinStream\Writer;

$reader = Reader::fromPathname(__DIR__ . '/path/to/file.bin');
$reader->readAs('array<int8, 42>'); // Reads binary as int8[42] array

$writer = Writer::fromPathname(__DIR__ . '/path/to/file.bin');
$writer->writeAs([1, 2, 3], 'array<int8>'); // Writes binary as int8[3] array
```

For example, below is a list of the behavior of some of the predefined types.

```php
use Serafim\BinStream\Reader;

$reader = Reader::fromPathname(__DIR__ . '/path/to/file.bin');

// Array: array<[TYPE = uint8], [COUNT = -1]>
$reader->readAs('array');               // array(0) {} - Empty array
$reader->readAs('array<uint32>');       // array(0) {} - Empty array
$reader->readAs('array<uint32, 2>');    // array(2) { 0 => int(x), 1 => int(x) }
$reader->readAs('array<uint32, -1>');   // array(0) {} - Empty array
$reader->readAs('array<array<int8, 10>, 2>');   // array(2) { 0 => array(10) {...}, ... }

// Bitmask: bitmask<[COUNT = 1]>
$reader->readAs('bitmask');             // array(8) { 0 => bool(x), 1 => ... }
$reader->readAs('bitmask<5>');          // array(40) { 0 => bool(x), 1 => ... }
$reader->readAs('bitmask<-1>');         // array(0) {}  - Empty array

// Flags: flags<[CLASS], [TYPE = uint8]>
$reader->readAs('flags<PHP\Enum\Type>')         // array(x) { 0 => enum(PHP\Enum\Type::CASE), ... }
$reader->readAs('flags<PHP\Enum\Type, uint32>') // array(x) { 0 => enum(PHP\Enum\Type::CASE), ... }

// Char: char
$reader->readAs('char');                // string(1) "X"

// String: string<[SIZE = -1]>
$reader->readAs('string');              // string(x) "..."
                                        // Reads data up to the first incoming \x00 char.
$reader->readAs('string<42>');          // string(42) "..."
$reader->readAs('string<-1>');          // string(0) ""

// Enum: enum<[CLASS], [TYPE = uint32]>
$reader->readAs('enum<PHP\Enum\Type>');         // enum(PHP\Enum\Type::class)
$reader->readAs('enum<PHP\Enum\Type, uint8>');  // enum(PHP\Enum\Type::class)

// Timestamp: timestamp<[TYPE = uint32], [IMMUTABLE = true]>
$reader->readAs('timestamp');                   // object(\DateTimeImmutable)
$reader->readAs('timestamp<uint32>');           // object(\DateTimeImmutable)
$reader->readAs('timestamp<uint32, false>');    // object(\DateTime)

// Scalars
$reader->readAs('float32');                     // float(0.0)
$reader->readAs('float64');                     // float(0.0)
$reader->readAs('int8');                        // int(0)
$reader->readAs('uint8');                       // int(0)
$reader->readAs('int16');                       // int(0)
$reader->readAs('uint16');                      // int(0)
$reader->readAs('int32');                       // int(0)
$reader->readAs('uint32');                      // int(0)
$reader->readAs('int64');                       // int(0)
$reader->readAs('uint64');                      // int(0)

// The "uint16", "uint32", "uint64", "float32" and "float64"
// allow endianness argument:
$reader->readAs('uint32<le>'); // Little-endian (default)
$reader->readAs('uint32<be>'); // Big-endian
$reader->readAs('uint32<me>'); // Machine-endian (auto)
```

