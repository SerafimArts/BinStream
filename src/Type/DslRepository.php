<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Type;

use Phplrt\Contracts\Exception\RuntimeExceptionInterface;
use Phplrt\Contracts\Parser\ParserInterface;
use Phplrt\Lexer\Lexer;
use Phplrt\Parser\BuilderInterface;
use Phplrt\Parser\ContextInterface;
use Phplrt\Parser\Parser;
use Phplrt\Parser\ParserConfigsInterface;
use Serafim\BinStream\Dsl\Node\Literal\Literal;
use Serafim\BinStream\Dsl\Node\Stmt\TypeStmt;

class DslRepository implements RepositoryInterface
{
    /**
     * @var ParserInterface
     */
    private ParserInterface $parser;

    /**
     * @var array<non-empty-string, TypeInterface>
     */
    private array $types = [];

    /**
     * @param Repository $parent
     */
    public function __construct(
        private readonly Repository $parent = new Repository()
    ) {
        $grammar = require __DIR__ . '/../../resources/dsl.php';

        $this->parser = new Parser(
            lexer: new Lexer(
            tokens: $grammar['tokens']['default'], skip: $grammar['skip']
        ), grammar: $grammar['grammar'], options: [
            ParserConfigsInterface::CONFIG_INITIAL_RULE => $grammar['initial'],
            ParserConfigsInterface::CONFIG_AST_BUILDER  => new class($grammar['reducers']) implements BuilderInterface {
                public function __construct(
                    private readonly array $reducers
                ) {
                }

                /**
                 * {@inheritDoc}
                 */
                public function build(ContextInterface $context, mixed $result): mixed
                {
                    $state = $context->getState();

                    if (isset($this->reducers[$state])) {
                        return ($this->reducers[$state])($context, $result);
                    }

                    return $result;
                }
            }
        ]
        );
    }


    /**
     * @param string $alias
     * @return void
     */
    private function assertValidAlias(string $alias): void
    {
        $prefix = \sprintf('Can not register type alias "%s": ', $alias);
        $alias = \strtolower($alias);

        if (\in_array($alias, ['true', 'false'], true)) {
            throw new \InvalidArgumentException(
                \sprintf('%s: The given name is reserved for a bool literal', $prefix)
            );
        }

        if (Endianness::tryFrom($alias)) {
            throw new \InvalidArgumentException(
                \sprintf('%s: The given name is reserved for a endianness literal', $prefix)
            );
        }

        if ($alias === 'null') {
            throw new \InvalidArgumentException(
                \sprintf('%s: The given name is reserved for a NULL literal', $prefix)
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function alias(string $class, array|string $aliases): static
    {
        foreach ((array)$aliases as $alias) {
            $this->assertValidAlias($alias);
        }

        $this->parent->alias($class, $aliases);

        return $this;
    }

    /**
     * @param string $type
     * @return TypeInterface
     * @throws RuntimeExceptionInterface
     * @throws \Throwable
     */
    public function get(string $type): TypeInterface
    {
        try {
            return $this->types[$type] ??= $this->make(
                $this->parser->parse($type)
            );
        } catch (\Error $e) {
            throw new \InvalidArgumentException(
                'An error occurred while initializing "' . $type . '": ' . $e->getMessage()
            );
        }
    }

    /**
     * @param TypeStmt $type
     * @return TypeInterface
     */
    private function make(TypeStmt $type): TypeInterface
    {
        $args = [];

        foreach ($type->args as $arg) {
            $args[] = $arg instanceof Literal
                ? $arg->getValue()
                : $this->argument($arg)
            ;
        }

        return $this->parent->get($type->name->name, $args);
    }

    /**
     * @param TypeStmt $stmt
     * @return TypeInterface|string
     */
    public function argument(TypeStmt $stmt): TypeInterface|string
    {
        if ($this->isNonTypeClass($stmt)) {
            return $stmt->name->name;
        }

        return $this->make($stmt);
    }

    /**
     * @param TypeStmt $stmt
     * @return bool
     */
    private function isNonTypeClass(TypeStmt $stmt): bool
    {
        return $stmt->args === []
            && \class_exists($stmt->name->name)
            && !\is_subclass_of($stmt->name->name, TypeInterface::class, true)
        ;
    }
}
