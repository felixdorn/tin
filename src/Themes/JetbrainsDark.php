<?php

declare(strict_types=1);

namespace Felix\Tin\Themes;

use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;

class JetbrainsDark extends Theme
{
    /** {@inheritDoc} */
    public function color(TokenType $type): string
    {
        return match ($type) {
            TokenType::Keyword  => '204;102;50',
            TokenType::Variable => '152;118;170',
            TokenType::LineNumber, TokenType::Comment  => '128;128;128',
            TokenType::String   => '106;135;89',
            TokenType::Function, TokenType::NamedParameter, TokenType::Attribute => '255;198;109',
            TokenType::Number, TokenType::Html => '104;151;187',
            TokenType::Default => '169;183;198',
        };
    }
}
