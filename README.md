# tin

tin is a PHP code highlighter for the terminal.

[![Tests](https://github.com/felixdorn/tin/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/felixdorn/tin/actions/workflows/tests.yml)
[![Formats](https://github.com/felixdorn/tin/actions/workflows/formats.yml/badge.svg?branch=main)](https://github.com/felixdorn/tin/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/felixdorn/tin/version)](//packagist.org/packages/felixdorn/tin)
[![Total Downloads](https://poser.pugx.org/felixdorn/tin/downloads)](//packagist.org/packages/felixdorn/tin)
[![License](https://poser.pugx.org/felixdorn/tin/license)](//packagist.org/packages/felixdorn/tin)

## Installation

> Requires [PHP 8.1+](https://php.net/releases)

You can install the package via composer:

```bash
composer require felixdorn/tin
```

## 🔞 Screenshots

![A piece of code highlighted using tin](art/screenshot.png)

Yes, this comes from a terminal.

## Usage

```php
<?php

use Felix\Tin\Themes\JetbrainsDark;
use Felix\Tin\Tin;

echo Tin::from(JetbrainsDark::class, $ansi = true)->highlight("<?php\n\necho 'Hello world';\n");
```

## Customizing the output

Apart from using a custom theme to change the colors, you have complete control over the highlighting proccess.

```php
$tin->process(
   $code,
   function (int $line, array $tokens, int $lineCount, \Felix\Tin\Themes\Theme $theme) {
        $lineNumber = $theme->apply(
            $theme->comment,
            str_pad((string)$line, strlen((string)$lineCount), ' ', STR_PAD_LEFT) .
            ' | '
        );

        return $lineNumber . implode('', $tokens) . PHP_EOL;
    }
);
```

> Returning null skips the line entirely.

## Themes

* [`Felix\Tin\Themes\JetbrainsDark`](src/Themes/JetbrainsDark.php)
* [`Felix\Tin\Themes\OneDark`](src/Themes/OneDark.php)

### Creating a theme

You need to extend `Felix\Tin\Themes\Theme` and set the colors to whatever you want.

The color are RGB values separated by a `;`.

```php
use Felix\Tin\Themes\Theme;

class OneDark extends Theme
{
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
```

## Future

* PHPDoc

## Known Issues

Named parameters are simply ignored by the built-in PHP parser which means that if a named parameter is also a
keyword such as `for`. The highlighter won't pick up on it and will highlight it as a keyword rather than a named
parameter.

There is no solution to that problem unless we implement our own parser (no) or the parser gets fixed,

## Testing

```bash
composer test
```

**tin** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
