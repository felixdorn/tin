<?php

namespace Felix\Tin\Themes;

use Felix\Tin\Contracts\ThemeInterface;
use Felix\Tin\Enums\TokenType;

class NullThemeInterface extends ThemeInterface
{
    public function color(TokenType $type): string
    {
        return '';
    }
}
