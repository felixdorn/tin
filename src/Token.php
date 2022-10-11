<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Enums\TokenType;
use PhpToken;

class Token
{
    protected function __construct(
        public readonly TokenType $type,
        public readonly int $id,
        public readonly string $text,
    ) {
    }

    public static function fromPhpToken(int $id, PhpToken $token): Token
    {
        return new self(TokenType::fromId($id), $id, $token->text);
    }

    /** @return self A clone of the token with the updated text */
    public function withText(string $text): Token
    {
        return new self($this->type, $this->id, $text);
    }
}
