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

/**
 * @template-extends Type<positive-int|0>
 */
abstract class UIntType extends IntType
{
    /**
     * @var Endianness
     */
    public readonly Endianness $endianness;

    /**
     * @param positive-int $size
     */
    public function __construct(
        int $size,
        Endianness|string $endianness = Endianness::DEFAULT,
    ) {
        $this->endianness = Endianness::parse($endianness);

        parent::__construct($size);
    }

    /**
     * {@inheritDoc}
     */
    abstract public function parse(ReadableStreamInterface $stream): int;
}
