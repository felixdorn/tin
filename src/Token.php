<?php

namespace Felix\Tin;

use Felix\Tin\Enums\TokenType;
use PhpToken;

class Token
{
    public function __construct(
        public readonly TokenType $type,
        public readonly int $id,
        public readonly string $text,
        public readonly int $line = -1,
        public readonly int $position = -1)
    {
    }

    public static function newUsing(int $id, PhpToken $token)
    {
        return new static(
            TokenType::fromPhpId($id),
            $id,
            $token->text,
            $token->line,
            $token->pos
        );
    }
}
