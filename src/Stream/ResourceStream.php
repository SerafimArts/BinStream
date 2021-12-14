<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Stream;

abstract class ResourceStream implements StreamInterface
{
    /**
     * @param resource $stream
     * @param bool $close
     */
    public function __construct(
        protected readonly mixed $stream,
        protected readonly bool $close = true
    ) {
        if (!\is_resource($stream)) {
            throw new \InvalidArgumentException(
                \sprintf('Passed argument must be a stream, but %s passed', \get_debug_type($stream))
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function seek(int $offset, Seek $seek = Seek::SET): int
    {
        return match ($seek) {
            Seek::SET => \fseek($this->stream, $offset),
            Seek::CURSOR => \fseek($this->stream, $offset, \SEEK_CUR),
        };
    }

    /**
     * {@inheritDoc}
     */
    public function offset(): int
    {
        return (int)\ftell($this->stream);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        if ($this->close && \is_resource($this->stream)) {
            \fclose($this->stream);
        }
    }
}
