<?php

use Felix\Tin\Line;
use Felix\Tin\Outputs\TestOutput;
use Felix\Tin\Themes\OneDark;
use Felix\Tin\Tin;
use Felix\Tin\Token;
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
    $hl = $this->tin->process("<?php\n1;\n2;\n3;", function (Felix\Tin\Line $line) {
        return $line->number % 2 === 0 ? $line->toString() : null;
    });

    expect($hl)->toMatchSnapshot();
});

it('can disable ansi', function () {
    $tin = Tin::from(OneDark::class, false);

    $hl = $tin->highlight($this->sample);

    expect($hl)->toMatchSnapshot();
});

test('line rendering is idempotent', function () {
    $queue = new SplQueue();
    // todo: this is unreadable
    $queue->push(Token::fromPhpToken(T_OPEN_TAG, new PhpToken(T_OPEN_TAG, '<?php')));
    $queue->push(Token::fromPhpToken(T_LNUMBER, new PhpToken(T_LNUMBER, '1')));
    $queue->push(Token::fromPhpToken(ord(';'), new PhpToken(ord(';'), ';')));

    $line = new Line(1, $queue, 1, new TestOutput());

    $a = $line->toString();
    $b = $line->toString();
    expect($b)->toBe($a);
});
