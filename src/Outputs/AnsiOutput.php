<?php

namespace Felix\Tin\Outputs;

use Felix\Tin\Contracts\OutputInterface;
use Felix\Tin\Contracts\ThemeInterface;
use Felix\Tin\Enums\TokenType;
use Felix\Tin\Line;

readonly class AnsiOutput implements OutputInterface
{
    public function __construct(
        protected ThemeInterface $theme,
        protected bool $ansiEnabled = true)
    {
    }

    public function transform(TokenType $type, string $value): string
    {
        if (!$this->ansiEnabled) {
            return $value;
        }

        return "\e[38;2;{$this->theme->color($type)}m{$value}\e[0m";
    }

    public function theme(): ThemeInterface
    {
        return $this->theme;
    }

    public function transformLine(Line $line): string
    {
        return str_pad(
            (string) $line->number,
            strlen((string) $line->totalCount),
            ' ',
            STR_PAD_LEFT
        ) . ' | ' . $line->toString() . PHP_EOL;
    }
}
