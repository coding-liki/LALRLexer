<?php
declare(strict_types=1);

namespace unit\Lexer;


use Codeception\Test\Unit;
use CodingLiki\GrammarParser\Token\GrammarTokenParser;
use CodingLiki\GrammarParser\Token\Token;
use CodingLiki\LALRLexer\Lexer\LALRLexer;

class LALRLexerTest extends Unit
{

    /**
     * @dataProvider parseSrcProvider
     * @param string $tokenTypesString
     * @param string $src
     * @param Token[] $tokens
     */
    public function testParseSrc(string $tokenTypesString, string $src, array $tokens)
    {
        $tokenTypes = GrammarTokenParser::parse($tokenTypesString);
        $lexer = new LALRLexer($tokenTypes);
        self::assertEquals($tokens, $lexer->parseSrc($src));
    }

    public function parseSrcProvider(): array
    {
        return [
            'void' => [
                '',
                '',
                [
                ]
            ],
            '1 tokenType' => [
                '
                a: a
                ',
                'aaa',
                [
                    new Token('a', 'a'),
                    new Token('a', 'a'),
                    new Token('a', 'a'),
                ],
            ],
            '2 tokenType' => [
                '
                A: a
                B: b
                ',
                'aaba',
                [
                    new Token('A', 'a'),
                    new Token('A', 'a'),
                    new Token('B', 'b'),
                    new Token('A', 'a'),
                ]
            ],
            '1 regex' => [
                '
                A: [abc]
                ',
                'abc',
                [
                    new Token('A', 'a'),
                    new Token('A', 'b'),
                    new Token('A', 'c'),
                ]
            ],
            '3 regex' => [
                '
                NUM: ([1-9][0-9]*|0)(?=\\s|$)
                ID: [a-zA-Z_][a-zA-Z_0-9]*
                WS_STRING: [\s]+
                ',
                '123 hello',
                [
                    new Token('NUM', '123'),
                    new Token('WS_STRING', ' '),
                    new Token('ID', 'hello'),
                ]
            ]
        ];
    }

    /**
     * @dataProvider parseSrcWithExceptListProvider
     * @param string $tokenTypesString
     * @param string $src
     * @param array $exceptList
     * @param array $tokens
     */
    public function testParseSrcWithExceptList(string $tokenTypesString, string $src, array $exceptList, array $tokens): void
    {
        $tokenTypes = GrammarTokenParser::parse($tokenTypesString);
        $lexer = new LALRLexer($tokenTypes, $exceptList);
        self::assertEquals($tokens, $lexer->parseSrc($src));
    }

    public function parseSrcWithExceptListProvider(): array
    {
        return [
            'all void' => [
                '',
                '',
                [
                ],
                [
                ]
            ],
            '1 tokenType, void except list' => [
                '
                a: a
                ',
                'aaa',
                [],
                [
                    new Token('a', 'a'),
                    new Token('a', 'a'),
                    new Token('a', 'a'),
                ],
            ],
            '1 tokenType, it is in except list' => [
                '
                a: a
                ',
                'aaa',
                ['a'],
                [
                ],
            ],
            '2 tokenTypes, one is in except list' => [
                '
                a: a
                b: b
                ',
                'ababbaa',
                ['a'],
                [
                    new Token('b', 'b'),
                    new Token('b', 'b'),
                    new Token('b', 'b'),
                ],
            ],
            '3 tokenTypes, two are in except list' => [
                '
                a: a
                b: b
                c: c
                ',
                'acbabbcaac',
                ['a', 'c'],
                [
                    new Token('b', 'b'),
                    new Token('b', 'b'),
                    new Token('b', 'b'),
                ],
            ]
        ];
    }
}
