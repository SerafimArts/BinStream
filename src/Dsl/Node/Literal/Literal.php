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
use Serafim\BinStream\Dsl\Node\Node;

abstract class Literal extends Node
{
    /**
     * @return mixed
     */
    abstract public function getValue(): mixed;
}

