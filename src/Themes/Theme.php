<?php

declare(strict_types=1);

namespace Felix\Tin\Themes;

use Felix\Tin\Enums\TokenType;

abstract class Theme
{
    /** @return string must be formatted as follow: (r);(g);(b) with (r), (g), (b), base 10 integers in the range [0-256) */
    abstract public function color(TokenType $type): string;
}
