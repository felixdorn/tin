<?php

namespace Felix\Tin\Contracts;

use Felix\Tin\Enums\TokenType;
use Felix\Tin\Line;

interface OutputInterface
{
    /** Transform the token value to the desired output format with respect to the token type and theme */
    public function transform(TokenType $type, string $value): string;

    public function transformLine(Line $line): ?string;

    /** Returns the theme used by the output */
    public function theme(): ThemeInterface;
}
