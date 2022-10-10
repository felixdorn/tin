<?php

declare(strict_types=1);

namespace Felix\Tin\Themes;

use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;

class OneDark extends Theme
{
    /** @var array<string,string> */
    protected array $colors = [
        'keyword'        => '199;120;221',
        'variable'       => '224;107;116',
        'comment'        => '91;98;110',
        'string'         => '152;195;121',
        'function'       => '98;174;239',
        'number'         => '229;192;122',
        'attribute'      => '98;174;239',
        'namedParameter' => '98;174;239',
        'default'        => '171;178;191',
    ];

    public function color(TokenType $type): string
    {
        return $this->colors[$type->value] ?? $this->colors['default'];
    }
}
