# Bin Stream

Binary stream reader and writer.

## Installation

`composer require serafim/bin-stream`

[Package on packagist.org](https://packagist.org/packages/serafim/bin-stream)

## Introduction

Bin stream package provides the ability to read and write binary sources.

## Basic API

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

## DSL

```php
use Serafim\BinStream\Reader;
use Serafim\BinStream\Writer;

$reader = Reader::fromPathname(__DIR__ . '/path/to/file.bin');
$reader->readAs('array<int8, 42>'); // Reads binary as int8[42] array

$writer = Writer::fromPathname(__DIR__ . '/path/to/file.bin');
$writer->writeAs([1, 2, 3], 'array<int8>'); // Writes binary as int8[3] array
```

### DSL Reader

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

