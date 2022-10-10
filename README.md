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

![A piece of code highlighted using tin ](art/screenshot.png)

![Another piece of code highlighted using tin](art/screenshot2.png)

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
   function (\Felix\Tin\Line $line) {
        $lineNumber = $line->output->transform(
            \Felix\Tin\Enums\TokenType::Comment,
            str_pad(
                (string) $line->number,
                strlen((string) $line->totalCount), ' ',
                STR_PAD_LEFT
            ) . ' | ',
        );

        return $lineNumber . $line->toString()  . PHP_EOL;
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
use Felix\Tin\Contracts\Theme;
use Felix\Tin\Enums\TokenType;

class OneDark implements Theme
{
        /** @var array<string,string>  */
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
```

## Future

* PHPDoc

## Testing

```bash
composer test
```

**tin** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
