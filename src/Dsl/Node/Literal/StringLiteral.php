<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\BinStream\Dsl\Node\Literal;

use Phplrt\Lexer\Token\CompositeTokenInterface;

class StringLiteral extends Literal
{
    /**
     * @var non-empty-array<non-empty-string, non-empty-string>
     */
    private const SPECIAL_CHARS = [
        '\\\\' => "\\",
        '\n' => "\n",
        '\r' => "\r",
        '\t' => "\t",
        '\v' => "\v",
        '\e' => "\e",
        '\f' => "\f",
        '\"' => '"',
    ];

    /**
     * @param positive-int|0 $offset
     * @param string $value
     */
    public function __construct(int $offset, public readonly string $value)
    {
        parent::__construct($offset);
    }

    /**
     * @param CompositeTokenInterface $token
     * @return static
     */
    public static function parse(CompositeTokenInterface $token): self
    {
        $value = $token[1]->getValue();

        // Apply special chars
        $value = \str_replace(\array_keys(self::SPECIAL_CHARS), \array_values(self::SPECIAL_CHARS), $value);

        // Apply sequences
        // TODO
        //  - \x[0-9A-Fa-f]{1,2}
        //  - \u{[0-9A-Fa-f]+}

        return new self($token->getOffset(), $value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
