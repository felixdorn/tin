<?php

namespace Felix\Tin\Contracts;

use Felix\Tin\Enums\TokenType;

interface OutputInterface
{
    /** Transform the token value to the desired output format with respect to the token type and theme */
    public function transform(TokenType $type, string $value): string;

    /** Returns a new line, usually PHP_EOL or <br /> */
    public function newLine(): string;

    /** Returns the theme used by the output */
    public function theme(): Theme;
}
