# tin

tin is a high-performance code highlighter for the terminal.

[![Tests](https://github.com/felixdorn/tin/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/felixdorn/tin/actions/workflows/tests.yml)
[![Formats](https://github.com/felixdorn/tin/actions/workflows/formats.yml/badge.svg?branch=master)](https://github.com/felixdorn/tin/actions/workflows/formats.yml)
[![Version](https://poser.pugx.org/felixdorn/tin/version)](//packagist.org/packages/felixdorn/tin)
[![Total Downloads](https://poser.pugx.org/felixdorn/tin/downloads)](//packagist.org/packages/felixdorn/tin)
[![License](https://poser.pugx.org/felixdorn/tin/license)](//packagist.org/packages/felixdorn/tin)

## Installation

> Requires [PHP 8.0.0+](https://php.net/releases)

You can install the package via composer:

```bash
composer require felixdorn/tin
```

## 🔞 Screenshots

![A piece of code highlighted using tin](art/screenshot.png)

## Usage

```php
use \Felix\Tin\Tin;
use \Felix\Tin\Themes\JetbrainsDark;

$theme = new JetbrainsDark()
$tin = new Tin($theme);

$tin->process("<?php\n\necho 'Hello world';", ansi: true)
```

You can disable the ansi output by passing `false` as the second parameter.

## Themes

* JetbrainsDark `Felix\Tin\Themes\JetbrainsDark`
* OneDark `Felix\Tin\Themes\OneDark`

### Creating a theme

You need to extend `Felix\Tin\Themes\Theme` and set the colors to whatever you want.

The color are RGB values separated by a `;`.

```php
use Felix\Tin\Themes\Theme;

class OneDark extends Theme
{
    public string $keyword  = '199;120;221';
    public string $variable = '224;107;116';
    public string $comment  = '91;98;110';
    public string $default  = '171;178;191';
    public string $string   = '152;195;121';
    public string $function = '98;174;239';
    public string $number   = '229;192;122';
}
```

## Performance

The code has been optimized a lot as i needed to highlight files quickly. Therefore, some compromise were made in terms
of code readability and simplicity.

It takes on average 0.0007 second per file.

To put that in context, highlighting the whole PHPUnit library takes ~265ms and around 2.8mb of memory

Highlighting the vendor directory of this package takes ~1.78s for ~13mb of memory. That's 1320 files per seconds.

> PHP built-in tokenizer for PHP uses most of the memory (around 80-90%)

You can check the full profiles here:

* [Highlighting PHPUnit](https://blackfire.io/profiles/2bd4c150-5226-4645-85fa-ffed43dc4602/graph)
* [Highlighting Vendor](https://blackfire.io/profiles/fa9b900f-d398-4efa-b999-9e7470b714b4/graph)

## Future
* PHPDoc
* Various outputs (cli / web)
* Line prefixes aka support for line numbers
* grayscale theme

## Testing

```bash
composer test
```

**tin** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
