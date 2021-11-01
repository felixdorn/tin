<?php

namespace Felix\Highlighter\Themes;

use Felix\Highlighter\Contracts\Theme;

class OneDark implements Theme
{
    public function keyword(): int
    {
        return 0xc778dd;
    }

    public function variable(): int
    {
        return 0xe06b74;
    }

    public function int(): int
    {
        return 0x98c379;
    }

    public function comment(): int
    {
        return 0x5b626e;
    }

    public function default(): int
    {
        return 0xabb2bf;
    }

    public function number(): int
    {
        return 0xe5c07a;
    }

    public function function(): int
    {
        return 0x62aeef;
    }

    public function string(): int
    {
        return 0x98c379;
    }
}
