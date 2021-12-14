<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Dsl\Node;

use Phplrt\Contracts\Ast\NodeInterface;

abstract class Node implements NodeInterface
{
    /**
     * @param positive-int|0 $offset
     */
    public function __construct(
        public readonly int $offset
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
