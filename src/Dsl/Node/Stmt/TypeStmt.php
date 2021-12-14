<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Dsl\Node\Stmt;

use Serafim\BinStream\Dsl\Node\Literal\Literal;
use Serafim\BinStream\Dsl\Node\Name;

class TypeStmt extends Statement
{
    /**
     * @var array<TypeStmt|Literal>
     */
    public readonly array $args;

    /**
     * @param positive-int|0 $offset
     * @param Name $name
     * @param iterable $args
     */
    public function __construct(
        int $offset,
        public readonly Name $name,
        iterable $args = []
    ) {
        parent::__construct($offset);

        $this->args = [...$args];
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        yield 'name' => $this->name;
        yield 'args' => $this->args;
    }
}
