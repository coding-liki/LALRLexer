<?php
declare(strict_types=1);

namespace CodingLiki\LALRLexer\Lexer;

use CodingLiki\GrammarParser\Token\Token;
use CodingLiki\GrammarParser\Token\TokenType;

class LALRLexer
{
    /**
     * LALRLexer constructor.
     * @param TokenType[] $tokenTypes
     */
    public function __construct(private array $tokenTypes, private array $exceptList = [])
    {
    }

    /**
     * @param string $src
     * @return Token[]
     */
    public function parseSrc(string $src): array
    {
        $tokens = [];
        $position = 0;
        while(!empty($src)){
            $nextToken = $this->parseNextToken($src);

            if($nextToken !== null) {

                $valueLength = strlen($nextToken->getValue());
                $src = substr($src, $valueLength);
                $position += $valueLength;
                if(in_array($nextToken->getType(), $this->exceptList, true)){
                    continue;
                }
                $tokens[] = $nextToken;
            } else {
                $tokens[] = new Token('ERROR_TOKEN', "bad symbol $src[0] at position = $position");
                break;
            }
        }

        return $tokens;
    }

    private function parseNextToken(string $src): ?Token
    {
        foreach ($this->tokenTypes as $type){
            $token = $this->parseTokenByType($src, $type);
            if($token !== null){
                return $token;
            }
        }
        return null;
    }

    private function parseTokenByType(string $src, TokenType $type): ?Token
    {
        $pattern = '/^'.$type->getRegex().'/u';
        $result = preg_match($pattern, $src, $matches);

        if(!empty($matches[0])){
            return new Token($type->getName(), $matches[0]);
        }

        return null;
    }

}