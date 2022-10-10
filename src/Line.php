<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Contracts\OutputInterface;
use SplQueue;

class Line
{
    /**
     * @param SplQueue<Token>|null $tokens
     */
    public function __construct(
        public readonly int $number,
        public readonly ?SplQueue $tokens,
        public readonly int $totalCount,
        public readonly OutputInterface $output,
    ) {
    }

    public function toString(): string
    {
        if ($this->tokens === null) {
            return '';
        }

        $buffer = '';

        while (!$this->tokens->isEmpty()) {
            /** @var Token $token */
            $token  = $this->tokens->pop();
            $buffer = $this->output->transform($token->type, $token->text) . $buffer;
        }

        // Make sure this method is idempotent.
        $this->tokens->rewind();

        return $buffer;
    }
}
