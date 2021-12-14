<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

use Serafim\BinStream\Stream\WritableStreamInterface;

class CharType extends StringType
{
    /**
     * CharType constructor.
     */
    public function __construct()
    {
        parent::__construct(1);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert(\is_string($data) && \strlen($data) === 1, new \InvalidArgumentException(
            'Expected char/string(1) type, but ' . \get_debug_type($data) . ' given'
        ));


        return parent::serialize($data, $stream);
    }
}
