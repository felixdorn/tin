<?php

use Felix\Highlighter\Highlighter;
use Felix\Highlighter\Themes\OneDark;
use PHPUnit\Framework\TestCase;
use function Spatie\Snapshots\assertMatchesTextSnapshot;

uses(TestCase::class);

it('can highlight', function () {
    $highlighter = new Highlighter(
        new OneDark()
    );

    $hl = $highlighter->process(
        file_get_contents(__DIR__ . '/fixtures/sample') ?: throw new RuntimeException('can not read sample file'),
    );

    assertMatchesTextSnapshot($hl);
});

it('does not highlight with ANSI disabled', function () {
    $code = file_get_contents(__DIR__ . '/fixtures/sample');
    $hl = new Felix\Highlighter\Highlighter(new OneDark());

    expect($hl->process($code, false))->toBe($code);
});
