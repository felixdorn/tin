<?php

namespace Felix\Highlighter\Contracts;

interface Theme
{
    public function keyword(): string;

    public function variable(): string;

    public function comment(): string;

    public function default(): string;

    public function string(): string;

    public function function(): string;

    public function number(): string;
}
