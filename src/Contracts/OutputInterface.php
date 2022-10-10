<?php

namespace Felix\Tin\Contracts;

use Felix\Tin\Enums\TokenType;
use Felix\Tin\Themes\Theme;

interface OutputInterface
{
    public function transform(TokenType $type, string $value): string;

    public function theme(): Theme;
}
