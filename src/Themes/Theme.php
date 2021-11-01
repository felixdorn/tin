<?php

namespace Felix\Tin\Themes;

class Theme
{
    public string $keyword;
    public string $variable;
    public string $comment;
    public string $default;
    public string $string;
    public string $function;
    public string $number;
    public string $attribute;

    public function __get(string $name): string
    {
        return $this->default;
    }
}
