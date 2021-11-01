<?php

namespace Felix\Highlighter\Themes;

use Felix\Highlighter\Contracts\Theme;

class OneDark implements Theme
{
    public function keyword(): string
    {
        return 'c778dd';
    }

    public function variable(): string
    {
        return 'e06b74';
    }

    public function string(): string
    {
        return '98c379';
    }

    public function comment(): string
    {
        return '5b626e';
    }

    public function default(): string
    {
        return 'abb2bf';
    }

    public function number(): string
    {
        return 'e5c07a';
    }

    public function function(): string
    {
        return '62aeef';
    }
}
