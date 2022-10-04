<?php

use Felix\Tin\Themes\OneDark;
use Felix\Tin\Tin;
use PHPUnit\Framework\TestCase;

use function Spatie\Snapshots\assertMatchesTextSnapshot;

uses(TestCase::class);

beforeEach(function () {
    $this->sample = file_get_contents(__DIR__ . '/fixtures/sample');
    $this->tin    = Tin::from(OneDark::class);
});

it('can highlight', function () {
    $hl = $this->tin->highlight($this->sample);

    assertMatchesTextSnapshot($hl);
});

it('can skip lines when processing', function () {
    $hl = $this->tin->process($this->sample, fn () => null);

    expect($hl)->toBeEmpty();
});

it('can process lines individually', function () {
    $hl = $this->tin->process("<?php\n1;\n2;\n3;", function (int $line, array $tokens, int $length) {
        return $line % 2 === 0 ? implode('', $tokens) : null;
    });

    expect($hl)->toMatchSnapshot();
});
