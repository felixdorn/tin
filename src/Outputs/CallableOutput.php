<?php

namespace Felix\Tin\Outputs;

use Felix\Tin\Contracts\OutputInterface;
use Felix\Tin\Contracts\ThemeInterface;
use Felix\Tin\Enums\TokenType;
use Felix\Tin\Line;

class CallableOutput implements OutputInterface
{
    /** @var callable */
    private $transformer;

    public function __construct(
        protected ThemeInterface $theme,
        callable $transformer
    ) {
        $this->transformer = $transformer;
    }

    public function transform(TokenType $type, string $value): string
    {
        return $value;
    }

    public function transformLine(Line $line): ?string
    {
        return ($this->transformer)($line);
    }

    public function theme(): ThemeInterface
    {
        return $this->theme;
    }
}
