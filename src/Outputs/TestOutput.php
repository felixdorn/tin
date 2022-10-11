<?php

namespace Felix\Tin\Outputs;

use Felix\Tin\Contracts\OutputInterface;
use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;
use Felix\Tin\Themes\NullTheme;

class TestOutput implements OutputInterface
{
    public function __construct()
    {
    }

    public function transform(TokenType $type, string $value): string
    {
        return $value;
    }

    public function newLine(): string
    {
        return PHP_EOL;
    }

    public function theme(): Theme
    {
        return new NullTheme();
    }
}
