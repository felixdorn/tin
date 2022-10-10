<?php

declare(strict_types=1);

namespace Felix\Tin;

use Felix\Tin\Themes\Theme;
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
        public readonly Theme $theme,
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
            $buffer = $this->theme->apply($token->type->value, $token->text) . $buffer;
        }

        // Make sure this method is idempotent.
        $this->tokens->rewind();

        return $buffer;
    }
}
