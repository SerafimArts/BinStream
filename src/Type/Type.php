<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

/**
 * @template T of mixed
 * @template-implements TypeInterface<T>
 */
abstract class Type implements TypeInterface
{
    /**
     * @param int $size
     */
    public function __construct(
        public readonly int $size
    ) {
        assert($this->size > 0, 'Type size must be greater than 0');
    }
}
