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

class NullLiteral extends Literal
{
    /**
     * @param positive-int|0 $offset
     */
    public function __construct(int $offset)
    {
        parent::__construct($offset);
    }

    /**
     * @param TokenInterface $token
     * @return static
     */
    public static function parse(TokenInterface $token): self
    {
        return new self($token->getOffset());
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return null;
    }
}
