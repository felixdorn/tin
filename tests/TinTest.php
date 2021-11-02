<?php

use Felix\Tin\Themes\OneDark;
use Felix\Tin\Tin;
use Felix\Tin\Token;
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

it('can customize highlighting output', function () {
    $tin = Tin::from(new OneDark());

    $output = $tin->process('<?php echo "Hello world";', function (Token $token, Token $lastToken) {
        return $token->id;
    });

    expect($output)->toBe(T_OPEN_TAG . T_ECHO . T_WHITESPACE . T_CONSTANT_ENCAPSED_STRING . ord(';'));
});
