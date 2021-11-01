<?php

namespace Felix\Highlighter;

class Reader
{
    public static array $types = ['string', 'int', 'float', 'object', 'callable', 'array', 'iterable', 'bool', 'self'];

    public array $tokens;
    public int $length;
    public int $cursor;

    public function __construct(string $code)
    {
        $this->tokens = token_get_all($code);
        $this->length = count($this->tokens);
        $this->cursor = 0;
    }

    public function current(): array
    {
        return $this->at($this->cursor);
    }

    public function at(int $index): array
    {
        $token = $this->tokens[$index];

        if (is_string($token)) {
            $kind = match ($token) {
                '[', ']' => T_BRACKET,
                '(', => T_OPEN_PARENTHESIS,
                ')' => T_CLOSE_PARENTHESIS,
                '{', '}' => T_BRACE,
                ','     => T_COMMA,
                '='     => T_EQUAL,
                ';'     => T_SEMICOLON,
                '.'     => T_CONCAT,
                ':'     => T_COLON,
                '!'     => T_NEGATION,
                '&'     => T_REF,
                default => $token
            };

            $token = [$kind, $token, null];
        }

        if ($token[0] !== T_STRING) {
            return $token;
        }

        return [$this->trueType($token, $index), $token[1], $token[2]];
    }

    private function trueType(array $token, int $index): int
    {
        $type = match ($token[1]) {
            'true'  => T_TRUE,
            'false' => T_FALSE,
            'null'  => T_NULL,
            default => null
        };

        if ($type !== null) {
            return $type;
        }

        $behind = $this->lookBehind($index);
        $ahead  = $this->lookAhead($index);

        if (in_array($token[1], static::$types)) {
            return T_BUILTIN_TYPE;
        }

        if ($ahead[0] === T_DOUBLE_COLON) {
            return T_CLASS_NAME;
        }

        return (match ($behind[0]) {
            T_NEW, T_USE, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_NAMESPACE, T_CLASS, T_INTERFACE, T_TRAIT, T_EXTENDS, T_IMPLEMENTS, T_INSTEADOF => fn () => T_CLASS_NAME,
            T_AS              => fn ()              => T_METHOD_NAME,
            T_DOUBLE_COLON    => fn ()    => $ahead[1] === '(' || in_array($ahead[0], [T_INSTEADOF, T_AS]) ? T_METHOD_NAME : T_CONST_NAME,
            T_OBJECT_OPERATOR => fn () => $ahead[1] === '(' ? T_METHOD_NAME : T_VARIABLE,
            T_FUNCTION        => function () use ($index) {
                $twoBehind = $this->lookBehind($index - 2);

                return in_array($twoBehind[0], [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC]) ? T_METHOD_NAME : T_FUNCTION_DECL;
            },
            default => function () use ($ahead, $behind) {
                if (in_array($behind[1], [':', '|', '?', ',', '('])) {
                    return T_CLASS_NAME;
                }

                if ($ahead[1] === '(') {
                    return T_FUNCTION_NAME;
                }

                return T_CONST_NAME;
            }
        })();
    }

    public function lookBehind(int $index): array
    {
        return $this->read($index - 1, ltr: false);
    }

    public function read(int $index, bool $ltr = true): array
    {
        $token = $this->tokens[$index];

        while ($token != null && $token[0] === T_WHITESPACE) {
            $index += $ltr ? 1 : -1;
            $token = $this->tokens[$index] ?? null;
        }

        if (is_string($token)) {
            return [-1, $token, null];
        }

        if (is_null($token)) {
            return [-1, '', null];
        }

        return $token;
    }

    public function lookAhead(int $index): array
    {
        return $this->read($index + 1);
    }

    public function eof(?int $index = null): bool
    {
        return ($index ?? $this->cursor) >= $this->length;
    }

    public function consume(): array
    {
        $this->cursor++;

        return $this->at($this->cursor - 1);
    }
}
