<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Dsl\Node;

use Phplrt\Contracts\Lexer\TokenInterface;

class Name extends Node
{
    /**
     * @var non-empty-string
     */
    public readonly string $name;

    /**
     * @var bool
     */
    public readonly bool $isSimple;

    /**
     * @param positive-int|0 $offset
     * @param non-empty-array<string> $parts
     */
    public function __construct(int $offset, public readonly array $parts)
    {
        $this->name = \implode('\\', $this->parts);
        $this->isSimple = \count($this->parts) === 1;

        parent::__construct($offset);
    }

    /**
     * @param non-empty-list<TokenInterface> $tokens
     * @return static
     */
    public static function parse(iterable $tokens): self
    {
        $offset = null;
        $parts = [];

        foreach ($tokens as $token) {
            $offset ??= $token->getOffset();
            $parts[] = $token->getValue();
        }

        return new self($offset, $parts);
    }
}
