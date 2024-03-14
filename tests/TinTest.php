<?php

use Felix\Tin\Line;
use Felix\Tin\Outputs\AnsiOutput;
use Felix\Tin\Outputs\CallableOutput;
use Felix\Tin\Outputs\HtmlOutput;
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
    $hl = (new Tin(new CallableOutput(
        new OneDark(),
        fn () => null
    )))->highlight($this->sample);

    expect($hl)->toBeEmpty();
});

it('can process lines individually', function () {
    $hl = (new Tin(new CallableOutput(
        $theme = new OneDark(),
        function (Line $line) use ($theme) {
            return $line->number % 2 === 0 ?
                (new AnsiOutput($theme))->transformLine($line) :
                null;
        }
    )))->highlight("<?php\n1;\n2;\n3;");

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

it('can render HTML', function () {
    $hl = (new Tin(
        new HtmlOutput(
            new OneDark()
        )
    ))->highlight($this->sample);

    expect($hl)->toMatchSnapshot();
});
