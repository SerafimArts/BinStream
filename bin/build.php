<?php

/**
 * This file is part of BinStream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Phplrt\Compiler\Compiler;

require __DIR__ . '/../vendor/autoload.php';

const GRAMMAR_INPUT = __DIR__ . '/../resources/dsl/grammar.pp2';
const GRAMMAR_OUTPUT = __DIR__ . '/../resources/dsl.php';

$grammar = (new Compiler())
    ->load(new SplFileInfo(GRAMMAR_INPUT))
    ->build()
        ->withClassUsage('Serafim\\BinStream\\Dsl\\Node')
;

file_put_contents(GRAMMAR_OUTPUT, $grammar->generate());
