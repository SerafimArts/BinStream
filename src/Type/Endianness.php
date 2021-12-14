<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

enum Endianness: string
{
    /**
     * Examples with the number 0x12_34_56_78:
     *  - 0x12 0x34 0x56 0x78
     */
    case BIG = 'be';

    /**
     * Examples with the number 0x12_34_56_78:
     *  - 0x78 0x56 0x34 0x12
     */
    case LITTLE = 'le';

    /**
     * Machine-aware endianness.
     */
    case MACHINE = 'me';

    /**
     * Default endianness.
     */
    public const DEFAULT = self::LITTLE;

    /**
     * @param Endianness|string $endianness
     * @return static
     */
    public static function parse(Endianness|string $endianness): self
    {
        if (\is_string($endianness)) {
            return self::from($endianness);
        }

        return $endianness;
    }
}
