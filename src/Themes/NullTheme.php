<?php

namespace Felix\Tin\Themes;

use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;

class NullTheme extends Theme
{
    public function color(TokenType $type): string
    {
        return '';
    }
}
