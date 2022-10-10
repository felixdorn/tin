<?php

namespace Felix\Tin;

use Felix\Tin\Themes\Theme;
use SplStack;

class Line
{
    /**
     * @param ?SplStack<Token> $tokens
     */
    public function __construct(
        public readonly int $number,
        public readonly ?SplStack $tokens,
        public readonly int $lineCount,
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
