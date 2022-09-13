<?php

use Felix\Tin\Themes\OneDark;
use Felix\Tin\Tin;
use PHPUnit\Framework\TestCase;

use function Spatie\Snapshots\assertMatchesTextSnapshot;

uses(TestCase::class);

it('can highlight', function () {
    $tin = Tin::from(new OneDark());

    $hl = $tin->highlight(
        file_get_contents(__DIR__ . '/fixtures/sample'),
    );

    assertMatchesTextSnapshot($hl);
});
