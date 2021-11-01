<?php

use Felix\Tin\Themes\OneDark;
use Felix\Tin\Tin;
use PHPUnit\Framework\TestCase;
use function Spatie\Snapshots\assertMatchesTextSnapshot;

uses(TestCase::class);

it('can highlight', function () {
    $Tin = new Tin(
        new OneDark()
    );

    $hl = $Tin->process(
        file_get_contents(__DIR__ . '/fixtures/sample') ?: throw new RuntimeException('can not read sample file'),
    );

    assertMatchesTextSnapshot($hl);
});

it('does not highlight with ANSI disabled', function () {
    $code = file_get_contents(__DIR__ . '/fixtures/sample');
    $hl = new Felix\Tin\Tin(new OneDark());

    expect($hl->process($code, false))->toBe($code);
});
