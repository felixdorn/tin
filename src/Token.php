<?php

namespace Felix\Highlighter;

use PhpToken;

class Token extends PhpToken
{
    protected static array $builtinTypes = ['string', 'int', 'float', 'object', 'callable', 'array', 'iterable', 'bool', 'self'];

    /** @var Token[] */
    private array $tokens = [];
    private int $index;

    /** @return Token[] */
    public static function tokenize(string $code, int $flags = 0): array
    {
        $tokens = parent::tokenize($code, $flags);

        return array_map(fn (Token $token, int $index) => $token->setTokens($tokens)->setIndex($index)->transform(), $tokens, array_keys($tokens));
    }

    protected function transform(): self
    {
        if (!$this->is(T_STRING)) {
            return $this;
        }

        if ($this->is(['true', 'false', 'null'])) {
            return $this->setId(T_BUILTIN_TYPE);
        }

        if ($this->is(static::$builtinTypes)) {
            return $this->setId(T_BUILTIN_TYPE);
        }

        $behind = $this->lookBehind($this->index);
        $ahead  = $this->lookAhead($this->index);

        if ($ahead->is(T_DOUBLE_COLON)) {
            return $this->setId(T_CLASS_NAME);
        }

        $id = match ($behind->id) {
            T_AS => T_METHOD_NAME,
            T_NEW, T_USE, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_NAMESPACE, T_CLASS, T_INTERFACE, T_TRAIT, T_EXTENDS, T_IMPLEMENTS, T_INSTEADOF => T_CLASS_NAME,
            T_DOUBLE_COLON    => $ahead->is('(') || $ahead->is([T_INSTEADOF, T_AS]) ? T_METHOD_NAME : T_CONST_NAME,
            T_OBJECT_OPERATOR => $ahead->is('(') ? T_METHOD_NAME : T_VARIABLE,
            T_FUNCTION        => $this->lookBehind($this->index - 2)->is([T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC]) ? T_METHOD_NAME : T_FUNCTION_DECL,
            default           => function () use ($ahead, $behind) {
                if ($behind->is([':', '|', '?', ',', '('])) {
                    return T_CLASS_NAME;
                }

                return $ahead->is('(') ? T_FUNCTION_NAME : T_CONST_NAME;
            }
        };

        return $this->setId($id);
    }

    private function setId(int|callable $id): self
    {
        if (is_callable($id)) {
            $value = $id();

            $this->id = $value;

            return $this;
        }

        $this->id = $id;

        return $this;
    }

    public function lookBehind(int $index): Token
    {
        return $this->read($index - 1, ltr: false);
    }

    public function read(int $index, bool $ltr = true): Token
    {
        $token = $this->tokens[$index] ?? null;

        while ($token != null && $token->is(T_WHITESPACE)) {
            $index += $ltr ? 1 : -1;
            $token = $this->tokens[$index] ?? null;
        }

        if (is_null($token)) {
            return new self(0, '');
        }

        return $token;
    }

    public function lookAhead(int $index): Token
    {
        return $this->read($index + 1);
    }

    protected function setIndex(int $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function setTokens(array $tokens): self
    {
        $this->tokens = $tokens;

        return $this;
    }
}
