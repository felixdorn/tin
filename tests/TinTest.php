<?php

use Felix\Tin\Themes\OneDark;
use Felix\Tin\Tin;
use PHPUnit\Framework\TestCase;

use function Spatie\Snapshots\assertMatchesTextSnapshot;

uses(TestCase::class);

it('can highlight', function () {
    $tin = Tin::from(OneDark::class);

    $hl = $tin->highlight(
        file_get_contents(__DIR__ . '/fixtures/sample'),
    );

    assertMatchesTextSnapshot($hl);
});

it('can skip lines', function () {
    $tin = Tin::from(OneDark::class);

    $hl = $tin->process(file_get_contents(__DIR__ . '/fixtures/sample'), fn () => null);

    expect($hl)->toBeEmpty();
});
