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
 * @template T of int
 * @template-extends TypeInterface<T>
 */
interface IntTypeInterface extends TypeInterface
{
    /**
     * @param ReadableStreamInterface $stream
     * @return T
     */
    public function parse(ReadableStreamInterface $stream): int;

    /**
     * @param T $data
     * @param WritableStreamInterface $stream
     * @return positive-int|0
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int;
}
