# tin

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

## Usage


## Performance

The code has been optimized a lot as I needed to highlight thousands of files quickly with a low-memory usage.

It takes on average 0.0007 second per file.

To put that in context, highlighting the whole PHPUnit library takes ~265ms and around 3mb of memory. The memory
overhead of the highlighting is small as just tokenizing takes 2.8mb of memory.

Highlighting the vendor directory of this package takes ~1.78s. That's 1320 files per seconds for ~13mb of memory (as we
load these files in memory).

You can check the full profiles here:

* [Tokenizing and Highlighting](https://blackfire.io/profiles/ee4f4620-4712-4efa-92a8-446ad0677744/graph)
* [Tokenizing](https://blackfire.io/profiles/ee4f4620-4712-4efa-92a8-446ad0677744/graph)

## Testing

```bash
composer test
```

**tin** was created by **[Félix Dorn](https://twitter.com/afelixdorn)** under
the **[MIT license](https://opensource.org/licenses/MIT)**.
