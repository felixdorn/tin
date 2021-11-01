<?php

namespace Felix\Highlighter\Contracts;

interface Theme
{
    public function keyword(): int;

    public function string(): int;

    public function variable(): int;

    public function comment(): int;

    public function default(): int;

    public function int(): int;

    public function function(): int;

    public function number(): int;
}
