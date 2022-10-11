<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Enums\TokenType;
use PhpToken;

class Token
{
    public function __construct(
        public readonly TokenType $type,
        public readonly int $id,
        public readonly string $text,
    ) {
    }

    public static function fromPhpToken(int $id, PhpToken $token): Token
    {
        return new self(
            TokenType::fromPhpId($id),
            $id,
            $token->text,
        );
    }

    public function withText(string $text): self
    {
        return new self($this->type, $this->id, $text);
    }
}
