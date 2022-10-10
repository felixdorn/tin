<?php

declare(strict_types=1);

namespace Felix\Tin\Themes;

class JetbrainsDark extends Theme
{
    /** @var array<string,string> */
    public array $colors = [
        'keyword'        => '204;102;50',
        'variable'       => '152;118;170',
        'comment'        => '128;128;128',
        'string'         => '106;135;89',
        'function'       => '255;198;109',
        'number'         => '104;151;187',
        'attribute'      => '187;181;41',
        'namedParameter' => '70;124;218',
        'default'        => '169;183;198',
    ];

    protected function color(string $name): string
    {
        return $this->colors[$name] ?? $this->colors['default'];
    }
}
