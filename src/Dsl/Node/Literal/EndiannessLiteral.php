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
use Serafim\BinStream\Dsl\Node\Name;
use Serafim\BinStream\Type\Endianness;

class EndiannessLiteral extends Literal
{
    /**
     * @param int $offset
     * @param Endianness $value
     */
    public function __construct(int $offset, public readonly Endianness $value)
    {
        parent::__construct($offset);
    }

    /**
     * @param TokenInterface $token
     * @return static
     */
    public static function parse(TokenInterface $token): self
    {
        return new self($token->getOffset(), Endianness::parse(\strtolower($token->getValue())));
    }

    /**
     * @return Endianness
     */
    public function getValue(): Endianness
    {
        return $this->value;
    }
}
