<?php

use Felix\Highlighter\Highlighter;
use Felix\Highlighter\Themes\OneDark;

require __DIR__ . '/vendor/autoload.php';

$hl = new Highlighter(
    new OneDark()
);

echo $hl->process(
    file_get_contents(__DIR__ . "/tests/fixtures/code.php")
);
