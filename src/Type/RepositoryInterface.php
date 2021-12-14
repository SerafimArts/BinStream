<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

interface RepositoryInterface
{
    /**
     * @param class-string<TypeInterface> $class
     * @param non-empty-string|non-empty-array<non-empty-string> $aliases
     * @return $this
     */
    public function alias(string $class, string|array $aliases): static;

    /**
     * @param string $type
     * @return TypeInterface
     */
    public function get(string $type): TypeInterface;
}
