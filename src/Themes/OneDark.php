<?php

declare(strict_types=1);

namespace Felix\Tin\Themes;

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

    protected function color(string $name): string
    {
        return $this->colors[$name] ?? $this->colors['default'];
    }
}
