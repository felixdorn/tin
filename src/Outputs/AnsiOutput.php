<?php

namespace Felix\Tin\Outputs;

use Felix\Tin\Contracts\OutputInterface;
use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;

class AnsiOutput implements OutputInterface
{
    public function __construct(
        protected readonly Theme $theme,
        protected readonly bool $ansiEnabled = true)
    {
    }

    public function transform(TokenType $type, string $text): string
    {
        if (!$this->ansiEnabled) {
            return $text;
        }

        return "\e[38;2;{$this->theme->color($type)}m{$text}\e[0m";
    }

    public function theme(): Theme
    {
        return $this->theme;
    }
}
