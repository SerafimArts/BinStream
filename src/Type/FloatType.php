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
 * @template-extends FloatTypeInterface<float>
 */
abstract class FloatType implements FloatTypeInterface
{
    /**
     * @param positive-int $size
     */
    public function __construct(
        public readonly int $size,
    ) {}
}
