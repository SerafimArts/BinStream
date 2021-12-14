<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Dsl\Node\Literal;

use Phplrt\Contracts\Lexer\TokenInterface;

class IntLiteral extends Literal
{
    /**
     * @param positive-int|0 $offset
     * @param int $value
     */
    public function __construct(int $offset, public readonly int $value)
    {
        parent::__construct($offset);
    }

    /**
     * @param TokenInterface $token
     * @return static
     */
    public static function parse(TokenInterface $token): self
    {
        return new self($token->getOffset(), (int)$token->getValue());
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
