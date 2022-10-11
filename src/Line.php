<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Contracts\OutputInterface;
use SplQueue;

class Line
{
    /** @param SplQueue<Token> $tokens */
    public function __construct(
        public readonly int $number,
        public readonly SplQueue $tokens,
        public readonly int $totalCount,
        public readonly OutputInterface $output,
    ) {
    }

    /** Converts tokens to a string, does and must not change the internal state of the \SplQueue */
    public function toString(): string
    {
        $buffer = '';

        $tokens = clone $this->tokens;

        while (!$tokens->isEmpty()) {
            /** @var Token $token */
            $token  = $tokens->pop();
            $buffer = $this->output->transform($token->type, $token->text) . $buffer;
        }

        return $buffer;
    }
}
