<?php

namespace Felix\Highlighter\Themes;

use Felix\Highlighter\Contracts\Theme;

class JetbrainsDark implements Theme
{
    public function keyword(): int
    {
        return 0xCC7832;
    }

    public function variable(): int
    {
        return 0x9876AA;
    }

    public function int(): int
    {
        return 0x6A8759;
    }

    public function comment(): int
    {
        return 0x808080;
    }

    public function default(): int
    {
        return 0xA9B7C6;
    }

    public function number(): int
    {
        return 0x6897BB;
    }

    public function function(): int
    {
        return 0xFFC66D;
    }

    public function string(): int
    {
        return 0x6A8759;
    }
}
