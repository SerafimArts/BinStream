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
use Serafim\BinStream\Stream\WritableStreamInterface;

/**
 * @template-extends ArrayType<bool>
 */
class BitMaskType extends ArrayType
{
    /**
     * @param int $count
     * @psalm-suppress InvalidArgument
     */
    public function __construct(int $count = 1)
    {
        parent::__construct(new CharType(), $count);
    }

    /**
     * {@inheritDoc}
     */
    public function parse(ReadableStreamInterface $stream): array
    {
        $result = [];

        /** @var string $byte */
        foreach (parent::parse($stream) as $byte) {
            $bits = \str_pad(\decbin(\ord($byte)), 8, '0', \STR_PAD_LEFT);

            foreach (\str_split($bits) as $bit) {
                $result[] = (bool)(int)$bit;
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(mixed $data, WritableStreamInterface $stream): int
    {
        assert(\is_array($data) && \array_is_list($data), new \InvalidArgumentException(
            'Expected list<bool> type, but ' . \get_debug_type($data) . ' given'
        ));

        /** @var array<string> $bytes */
        $bytes = [];

        while ($data !== []) {
            $byte = '';

            for ($i = 0; $i < 8; ++$i) {
                $byte .= (int)(\array_shift($data) ?? false);
            }

            $bytes[] = \chr((int)\bindec($byte));
        }

        /** @psalm-suppress InvalidScalarArgument */
        return parent::serialize($bytes, $stream);
    }
}
