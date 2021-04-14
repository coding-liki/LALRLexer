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
                    new Token('EOF', ''),
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
                    new Token('EOF', ''),
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
                    new Token('EOF', ''),
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
                    new Token('EOF', ''),
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
                    new Token('EOF', ''),
                ]
            ]
        ];
    }
}
