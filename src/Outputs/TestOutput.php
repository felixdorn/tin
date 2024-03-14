<?php

namespace Felix\Tin\Outputs;

use Felix\Tin\Contracts\OutputInterface;
use Felix\Tin\Contracts\ThemeInterface;
use Felix\Tin\Enums\TokenType;
use Felix\Tin\Line;
use Felix\Tin\Themes\NullThemeInterface;

readonly class TestOutput implements OutputInterface
{
    public function __construct()
    {
    }

    public function transform(TokenType $type, string $value): string
    {
        return $value;
    }

    public function theme(): ThemeInterface
    {
        return new NullThemeInterface();
    }

    public function transformLine(Line $line): string
    {
        return $line->toString() . PHP_EOL;
    }
}
