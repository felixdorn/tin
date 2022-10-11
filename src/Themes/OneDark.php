<?php

declare(strict_types=1);

namespace Felix\Tin\Themes;

use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;

class OneDark extends Theme
{
    /** {@inheritDoc} */
    public function color(TokenType $type): string
    {
        return match ($type) {
            TokenType::Keyword  => '199;120;221',
            TokenType::Variable => '224;107;116',
            TokenType::LineNumber, TokenType::Comment => '91;98;110',
            TokenType::String => '152;195;121',
            TokenType::Function, TokenType::NamedParameter, TokenType::Attribute => '98;174;239',
            TokenType::Number, TokenType::Html => '229;192;122',
            TokenType::Default => '171;178;191',
        };
    }
}
