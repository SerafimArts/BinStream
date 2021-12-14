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
 * @template-extends Type<float>
 */
abstract class FloatType extends Type
{
    /**
     * {@inheritDoc}
     */
    abstract public function parse(ReadableStreamInterface $stream): float;
}
