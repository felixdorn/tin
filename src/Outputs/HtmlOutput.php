<?php

namespace Felix\Tin\Outputs;

use Felix\Tin\Contracts\OutputInterface;
use Felix\Tin\Contracts\ThemeInterface;
use Felix\Tin\Enums\TokenType;
use Felix\Tin\Line;

readonly class HtmlOutput implements OutputInterface
{
    public function __construct(
        protected ThemeInterface $theme,
    ) {
    }

    public function transform(TokenType $type, string $value): string
    {
        return sprintf(
            '<span style="color:rgb(%s);">%s</span>',
            str_replace(';', ',', $this->theme->color($type)),
            $value
        );
    }

    public function theme(): ThemeInterface
    {
        return $this->theme;
    }

    public function transformLine(Line $line): string
    {
        return sprintf(
            '<div class="line"><span class="line-number">%s</span>%s</div>',
            $line->number, $line->toString()
        );
    }
}
