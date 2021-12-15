<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

use Serafim\BinStream\Type;

class Repository implements RepositoryInterface
{
    /**
     * @var non-empty-array<class-string<TypeInterface>, non-empty-string|non-empty-array<non-empty-string>>
     */
    final public const DEFAULT_ALIASES = [
        Type\ArrayType::class     => 'array',
        Type\BitMaskType::class   => 'bitmask',
        Type\CharType::class      => 'char',
        Type\StringType::class    => 'string',
        Type\EnumType::class      => 'enum',
        Type\FlagsType::class     => 'flags',
        Type\TimestampType::class => ['timestamp', 'date'],
        Type\Float32Type::class   => ['float32', 'float'],
        Type\Float64Type::class   => ['float64', 'double'],
        Type\Int8Type::class      => 'int8',
        Type\Int16Type::class     => 'int16',
        Type\Int32Type::class     => 'int32',
        Type\Int64Type::class     => 'int64',
        Type\UInt8Type::class     => 'uint8',
        Type\UInt16Type::class    => 'uint16',
        Type\UInt32Type::class    => 'uint32',
        Type\UInt64Type::class    => 'uint64',
    ];

    /**
     * @var array<non-empty-string, class-string<TypeInterface>>
     */
    private array $aliases = [];

    /**
     * @var array<non-empty-string, class-string<TypeInterface>>
     */
    private array $types = [];

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->bootDefaultAliases();
    }

    /**
     * @return void
     */
    protected function bootDefaultAliases(): void
    {
        foreach (self::DEFAULT_ALIASES as $fqn => $aliases) {
            $this->alias($fqn, $aliases);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function alias(string $class, string|array $aliases): static
    {
        foreach ((array)$aliases as $alias) {
            if ($alias === '') {
                throw new \InvalidArgumentException(
                    \sprintf('The given type alias name may not be empty', $alias)
                );
            }

            $this->aliases[$alias] = $class;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $type, array $args = []): TypeInterface
    {
        $type = $this->aliases[$type] ?? $type;

        if ($args === []) {
            return $this->types[$type] ??= $this->new($type, $args);
        }

        return $this->new($type, $args);
    }

    /**
     * @param string $type
     * @param array $args
     * @return TypeInterface
     */
    private function new(string $type, array $args): TypeInterface
    {
        if (!\class_exists($type)) {
            $message = \sprintf('Can not create type "%s"', $type);

            if ($similar = $this->similar($type)) {
                $message .= \sprintf(', did you mean "%s"?', $similar);
            }

            throw new \InvalidArgumentException($message);
        }

        return new $type(...$args);
    }

    /**
     * @param string $name
     * @return string|null
     */
    private function similar(string $name): ?string
    {
        foreach ($this->aliases as $alias => $type) {
            if (\levenshtein($name, $alias) === 1) {
                return $alias;
            }
        }

        return null;
    }
}
