<?php

namespace Felix\Highlighter\Themes;

use Felix\Highlighter\Contracts\Theme;

class JetbrainsDark implements Theme
{
    public function keyword(): string
    {
        return 'CC7832';
    }

    public function variable(): string
    {
        return '9876AA';
    }

    public function string(): string
    {
        return '6A8759';
    }

    public function comment(): string
    {
        return '808080';
    }

    public function default(): string
    {
        return 'A9B7C6';
    }

    public function number(): string
    {
        return '6897BB';
    }

    public function function(): string
    {
        return 'FFC66D';
    }
}
