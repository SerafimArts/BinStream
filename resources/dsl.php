<?php

use Serafim\BinStream\Dsl\Node;

return [
    'initial' => 0,
    'tokens' => [
        'default' => [
            'T_STRING_LITERAL' => '(L?)"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"',
            'T_FLOAT_LITERAL' => '\\-?(?:[1-9]\\d*|[0-9])\\.(?:[1-9]\\d*|[0-9])',
            'T_INT_LITERAL' => '\\-?[1-9]\\d*|[0-9]',
            'T_BOOL_LITERAL' => '(?i)(?:true|false)',
            'T_ENDIANNESS_LITERAL' => '(?i)(?:le|be|me)',
            'T_NULL_LITERAL' => '(?i)(?:null)',
            'T_NAME' => '[a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*',
            'T_LT' => '<',
            'T_GT' => '>',
            'T_COMMA' => ',',
            'T_DOUBLE_COLON' => '::',
            'T_NS_DELIMITER' => '\\\\',
            'T_WHITESPACE' => '\\s+',
            'T_BLOCK_COMMENT' => '\\h*/\\*.*?\\*/\\h*',
        ],
    ],
    'skip' => [
        'T_WHITESPACE',
        'T_BLOCK_COMMENT',
    ],
    'transitions' => [
        
    ],
    'grammar' => [
        0 => new \Phplrt\Grammar\Concatenation([8, 10]),
        1 => new \Phplrt\Grammar\Lexeme('T_NS_DELIMITER', false),
        2 => new \Phplrt\Grammar\Lexeme('T_NS_DELIMITER', false),
        3 => new \Phplrt\Grammar\Lexeme('T_NAME', true),
        4 => new \Phplrt\Grammar\Concatenation([2, 3]),
        5 => new \Phplrt\Grammar\Optional(1),
        6 => new \Phplrt\Grammar\Lexeme('T_NAME', true),
        7 => new \Phplrt\Grammar\Repetition(4, 0, INF),
        8 => new \Phplrt\Grammar\Concatenation([5, 6, 7]),
        9 => new \Phplrt\Grammar\Concatenation([14, 11, 15, 16]),
        10 => new \Phplrt\Grammar\Optional(9),
        11 => new \Phplrt\Grammar\Alternation([17, 0]),
        12 => new \Phplrt\Grammar\Lexeme('T_COMMA', false),
        13 => new \Phplrt\Grammar\Concatenation([12, 11]),
        14 => new \Phplrt\Grammar\Lexeme('T_LT', false),
        15 => new \Phplrt\Grammar\Repetition(13, 0, INF),
        16 => new \Phplrt\Grammar\Lexeme('T_GT', false),
        17 => new \Phplrt\Grammar\Alternation([18, 19, 20, 21, 22, 23, 24]),
        18 => new \Phplrt\Grammar\Lexeme('T_STRING_LITERAL', true),
        19 => new \Phplrt\Grammar\Lexeme('T_FLOAT_LITERAL', true),
        20 => new \Phplrt\Grammar\Lexeme('T_INT_LITERAL', true),
        21 => new \Phplrt\Grammar\Lexeme('T_ENDIANNESS_LITERAL', true),
        22 => new \Phplrt\Grammar\Lexeme('T_BOOL_LITERAL', true),
        23 => new \Phplrt\Grammar\Lexeme('T_NULL_LITERAL', true),
        25 => new \Phplrt\Grammar\Lexeme('T_DOUBLE_COLON', false),
        26 => new \Phplrt\Grammar\Lexeme('T_NAME', true),
        24 => new \Phplrt\Grammar\Concatenation([8, 25, 26])
    ],
    'reducers' => [
        8 => function (\Phplrt\Parser\Context $ctx, $children) {
            return Node\Name::parse($children);
        },
        0 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            $offset = $token->getOffset();
            return new Node\Stmt\TypeStmt(
            offset: $offset,
            name: $children[0],
            args: $children[1] ?? [],
        );
        },
        9 => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \ArrayIterator($children);
        },
        18 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\StringLiteral::parse($token);
        },
        19 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\FloatLiteral::parse($token);
        },
        20 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\IntLiteral::parse($token);
        },
        22 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\BoolLiteral::parse($token);
        },
        21 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\EndiannessLiteral::parse($token);
        },
        23 => function (\Phplrt\Parser\Context $ctx, $children) {
            $token = $ctx->getToken();
            return Node\Literal\NullLiteral::parse($token);
        },
        24 => function (\Phplrt\Parser\Context $ctx, $children) {
            return Node\Literal\ClassConstLiteral::parse($children[0], $children[1]);
        }
    ]
];
