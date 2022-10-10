<?php

declare(strict_types=1);

namespace Felix\Tin;

use Generator;
use PhpToken;

const AHEAD  = 1;
const BEHIND = -1;

class Tokenizer
{
    protected string $code;

    protected function __construct(string $code)
    {
        $this->code = rtrim(str_replace(["\r\n", "\r"], "\n", $code));
    }

    /**
     * @return Generator<Token>
     */
    public static function tokenize(string $code): Generator
    {
        return (new self($code))->process();
    }

    /** @return Generator<Token> */
    protected function process(): Generator
    {
        $raw         = PhpToken::tokenize($this->code, TOKEN_PARSE);
        $inAttribute = false;

        foreach ($raw as $index => $token) {
            if ($token->is(['true', 'false', 'null', 'string', 'int', 'float', 'object', 'callable', 'array', 'iterable', 'bool', 'self'])) {
                yield Token::newUsing(T_BUILTIN_TYPE, $token);
                continue;
            }

            if ($token->text === ':' && $index - 1 >= 0 && $this->idFromContext($raw, $index - 1) === T_NAMED_PARAMETER) {
                yield Token::newUsing(T_NAMED_PARAMETER, $token);
                continue;
            }

            if ($token->id === T_STRING) {
                yield Token::newUsing($this->idFromContext($raw, $index), $token);
                continue;
            }

            if ($token->id === T_ATTRIBUTE) {
                $inAttribute = true;
                yield Token::newUsing(T_ATTRIBUTE, $token);
                continue;
            }

            // Code ending with an attribute is most likely a _weird_ no-op, but it's valid.
            $next = $raw[$index + 1] ?? null;
            // Are we leaving the attribute?
            // (1) Current token is ']'
            // (2) Next token is whitespace or another attribute
            if ($inAttribute && $token->text === ']' && $next && $next->is([T_WHITESPACE, T_ATTRIBUTE])) {
                yield Token::newUsing(T_ATTRIBUTE_END, $token);
                $inAttribute = false;
                continue;
            }

            yield Token::newUsing($token->id, $token);
        }
    }

    /**
     * Find the real type of T_STRING token which is one of :.
     *
     *  - T_CLASS_NAME
     *  - T_FUNCTION_DECL
     *  - T_METHOD_NAME
     *  - T_FUNCTION_NAME
     *  - T_CONST_NAME
     *  - T_VARIABLE
     *  - T_DECLARE_PARAMETER
     *  - T_NAMED_PARAMETER
     *  - T_ATTRIBUTE_CLASS
     *
     * @param array<int, PhpToken> $tokens
     */
    protected function idFromContext(array $tokens, int $index): int
    {
        if ($tokens[$index]->id !== T_STRING) {
            return $tokens[$index]->id;
        }

        $ahead = $this->look(AHEAD, $tokens, $index + 1);

        // Foo::something()
        // |  ^ (ahead)
        // ^ (current)
        if ($ahead->id === T_DOUBLE_COLON) {
            return T_CLASS_NAME;
        }

        $behind    = $this->look(BEHIND, $tokens, $index - 1);
        $twoBehind = $this->look(BEHIND, $tokens, $index - 2);

        if ($behind->is(T_ENUM)) {
            return T_ENUM_NAME;
        }

        // foo(bar: 1)
        //     |  ^ (ahead)
        //     ^ (current)
        // While ':'  can be used in a return type, the preceding token would be a ')' or a T_ENUM.
        // We checked for T_ENUM and the current token must be a T_STRING, so we can safely assume
        // this is a named parameter.
        if ($ahead->text === ':') {
            return T_NAMED_PARAMETER;
        }

        $id = match ($behind->id) {
            // 58, 124, 63, 44 are the ASCII codes for ':', '|', '?', ','
            T_ENUM, T_NEW, T_USE, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_NAMESPACE, T_CLASS, T_INTERFACE, T_TRAIT, T_EXTENDS, T_IMPLEMENTS, T_INSTEADOF, 58, 124, 63, 44 => T_CLASS_NAME,
            // * private function foo()
            //   |       |        ^ (current)
            //   |       ^ (behind)
            //   ^ (two behind)
            // Note, private could be any of the access/visibility modifiers.
            // Otherwise, it's a function declaration.
            T_FUNCTION => $twoBehind->is([T_FINAL, T_ABSTRACT, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC, T_READONLY]) ? T_METHOD_NAME : T_FUNCTION_DECL,
            // * $foo->bar = ...;
            //       | |  ^ (ahead)
            //       | ^ (current)
            //       ^ (behind)
            // In which case, it's a T_VARIABLE
            // * $foo->bar();
            //       | |  ^ (ahead)
            //       | ^ (current)
            //       ^ (behind)
            // In which case, it's a T_METHOD_NAME
            T_OBJECT_OPERATOR => $ahead->text === '(' ? T_METHOD_NAME : T_VARIABLE,
            // * $foo::bar();
            //       | |  ^ (ahead)
            //       | ^ (current)
            //       ^ (behind)
            // * traitA::method insteadof traitB;
            //           |      ^ (ahead)
            //           ^ (current)
            // * use A as B;
            //       | ^ (ahead)
            //       ^ (current)
            // In all these cases, it's a T_CLASS_NAME
            // * $foo::bar = ...;
            //       | ^ (ahead)
            //       ^ (current)
            // In which case, it's a T_CONST_NAME
            // Note, T_DOUBLE_COLON is equal to T_PAAMAYIM_NEKUDOTAYIM, there's no need to handle both.
            T_DOUBLE_COLON => $ahead->text === '(' || $ahead->id === T_INSTEADOF || $ahead->id === T_AS ? T_METHOD_NAME : T_CONST_NAME,
            // TODO: explain this
            T_AS => T_METHOD_NAME,
            // * #[Foo]
            //   | ^ (current)
            //   ^ (behind)
            T_ATTRIBUTE => T_ATTRIBUTE_CLASS,
            default     => null
        };

        return $id !== null ? $id : match (true) {
            // * foo()
            //   |  ^ (ahead)
            //   ^ (current)
            // * foo::bar()
            //        |  ^ (ahead)
            //        ^ (current)
            // In both cases, it's a T_FUNCTION_NAME
            $ahead->text === '(' => T_FUNCTION_NAME,
            // * declare(strict_types=1);
            //   |      |^ (current)
            //   |      ^ (behind)
            //   ^ (two behind)
            // In which case, it's a T_DECLARE_PARAMETER
            $twoBehind->is(T_DECLARE) => T_DECLARE_PARAMETER,
            // public function foo(Bar $bar)
            //        |            ^ (current)
            //        |       |   |^ (behind)
            //        |       |   ^ (two behind)
            //        |       ^ (three behind)
            //        ^ (four behind)
            // This is where the $index - 4 comes from
            $this->look(BEHIND, $tokens, $index - 4)->is(T_FUNCTION) => T_CLASS_NAME,
            default                                                  => T_CONST_NAME
        };
    }

    /**
     * Finds the nearest non-whitespace token at a given index in a given direction.
     *
     * @param array<int, PhpToken> $tokens
     */
    protected function look(int $hs, array $tokens, int $index): PhpToken
    {
        $token = $tokens[$index];

        if ($token->id === T_WHITESPACE) {
            $token = $tokens[$index + $hs];
        }

        return $token;
    }
}
