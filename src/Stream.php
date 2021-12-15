<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream;

use Serafim\BinStream\Stream\Seek;
use Serafim\BinStream\Stream\StreamInterface;
use Serafim\BinStream\Type\DslRepository;
use Serafim\BinStream\Type\Repository;
use Serafim\BinStream\Type\RepositoryInterface;

/**
 * @template TStream of StreamInterface
 */
abstract class Stream implements StreamInterface
{
    /**
     * @var RepositoryInterface
     */
    public readonly RepositoryInterface $types;

    /**
     * @param TStream $stream
     * @param Repository $repository
     */
    public function __construct(
        protected readonly StreamInterface $stream,
        Repository $repository = new Repository(),
    ) {
        $this->types = new DslRepository($repository);
    }

    /**
     * @template T of mixed
     *
     * @param callable(static): T $handler
     * @return T
     */
    public function lookahead(callable $handler): mixed
    {
        $offset = $this->offset();
        $result = $handler($this);
        $this->seek($offset);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function offset(): int
    {
        return $this->stream->offset();
    }

    /**
     * {@inheritDoc}
     */
    public function seek(int $offset, Seek $seek = Seek::SET): int
    {
        return $this->stream->seek($offset, $seek);
    }
}
