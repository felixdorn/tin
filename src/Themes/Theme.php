<?php

declare(strict_types=1);

namespace Felix\Tin\Themes;

/**
 * @property string $keyword
 * @property string $variable
 * @property string $comment
 * @property string $string
 * @property string $function
 * @property string $number
 * @property string $attribute
 * @property string $namedParameter
 * @property string $default
 */
abstract class Theme
{
    public function __construct(protected bool $ansiEnabled = true)
    {
    }

    public function __get(string $name): string
    {
        return $name;
    }

    public function outputsAnsi(): bool
    {
        return $this->ansiEnabled;
    }

    public function ansi(bool $state): self
    {
        $this->ansiEnabled = $state;

        return $this;
    }

    public function sprintf(string $color, string $text, bool|float|int|string|null ...$args): string
    {
        return $this->apply($color, sprintf($text, ...$args));
    }

    public function apply(string $color, string $text): string
    {
        if (!$this->ansiEnabled) {
            return $text;
        }

        return "\033[38;2;{$this->color($color)}m$text\033[0m";
    }

    /**
     * @param string $name must be formatted as follow: (r);(g);(b) with:
     *                     (r), (g), (b) must returns a base 10 integer in [0-256).
     *                     Leading zeros should be trimmed (if any).
     *                     If $name is unknown to your theme, the function MUST return a constant default color,
     *                     that can also be retrieved if $name is `default`.
     */
    abstract protected function color(string $name): string;
}
