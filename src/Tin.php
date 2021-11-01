<?php

namespace Felix\Tin;

use Felix\Tin\Themes\Theme;
use PhpToken;

class Tin
{
    protected Theme $theme;

    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    public function process(string $code, bool $ansi = true): string
    {
        if (!$ansi) {
            return $code;
        }

        $highlighted = '';
        $tokens      = PhpToken::tokenize($code);

        foreach ($tokens as $index => $token) {
            if ($token->id !== T_STRING) {
                $id = $token->id;
            } elseif ($token->is(['true', 'false', 'null', 'string', 'int', 'float', 'object', 'callable', 'array', 'iterable', 'bool', 'self'])) {
                $id = T_BUILTIN_TYPE;
            } else {
                $id = $this->idFromContext($tokens, $index);
            }

            if ($id < 256) {
                $color = $this->theme->default;
            } else {
                $color = match ($id) {
                    T_METHOD_NAME, T_FUNCTION_DECL => $this->theme->function,
                    T_COMMENT, T_DOC_COMMENT => $this->theme->comment,
                    T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE => $this->theme->string,
                    T_LNUMBER, T_DNUMBER => $this->theme->number,
                    T_VARIABLE, T_CONST_NAME, T_NUM_STRING, T_CLASS_C, T_METHOD_C, T_NS_C, T_FUNC_C, T_TRAIT_C, T_DIR, T_FILE, T_LINE => $this->theme->variable,
                    T_ABSTRACT, T_ARRAY, T_FOREACH, T_AS, T_ECHO, T_TRY, T_CATCH, T_CLONE, T_CLOSE_TAG, T_SWITCH, T_CASE, T_BREAK, T_DEFAULT, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_CLASS, T_PROTECTED, T_PUBLIC, T_PRIVATE, T_FUNCTION, T_NEW, T_RETURN, T_CONST, T_CONTINUE, T_DO, T_ELSE, T_IF, T_ELSEIF, T_EMPTY, T_WHILE, T_ENDDECLARE, T_ENDFOR, T_ENDFOREACH, T_ENDIF, T_ENDSWITCH, T_ENDWHILE, T_START_HEREDOC, T_END_HEREDOC, T_EXIT, T_EVAL, T_EXTENDS, T_FINALLY, T_FINAL, T_FOR, T_GLOBAL, T_GOTO, T_HALT_COMPILER, T_IMPLEMENTS, T_INCLUDE, T_REQUIRE, T_INSTANCEOF, T_INSTEADOF, T_INTERFACE, T_ISSET, T_LIST, T_LOGICAL_AND, T_LOGICAL_XOR, T_LOGICAL_OR, T_NAMESPACE, T_PRINT, T_REQUIRE_ONCE, T_INCLUDE_ONCE, T_STATIC, T_THROW, T_TRAIT, T_UNSET, T_USE, T_VAR, T_YIELD, T_YIELD_FROM, T_MATCH, T_FN, T_DECLARE, T_BUILTIN_TYPE, T_BOOL_CAST, T_ARRAY_CAST, T_DOUBLE_CAST, T_INT_CAST, T_UNSET_CAST, T_OBJECT_CAST, T_STRING_CAST => $this->theme->keyword,
                    default => $this->theme->default
                };
            }
            $highlighted .= "\e[38;2;" . $color . 'm' . $token->text . "\e[0m";
        }

        return $highlighted;
    }

    /**
     * Find the real type of a T_STRING token which is one of :.
     *
     *  - T_CLASS_NAME
     *  - T_FUNCTION_DECL
     *  - T_METHOD_NAME
     *  - T_CONST_NAME
     *  - T_VARIABLE
     *  - T_DECLARE_PARAMETER
     *
     * @param array<int, PhpToken> $tokens
     */
    protected function idFromContext(array $tokens, int $index): int
    {
        $ahead = $this->read($tokens, $index + 1);

        if ($ahead->id === T_DOUBLE_COLON) {
            return T_CLASS_NAME;
        }

        $behind    = $this->read($tokens, $index - 1, ltr: false);
        $twoBehind = $this->read($tokens, $index - 2, ltr: false);

        return match ($behind->id) {
            T_NEW, T_USE, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_NAMESPACE, T_CLASS, T_INTERFACE, T_TRAIT, T_EXTENDS, T_IMPLEMENTS, T_INSTEADOF => T_CLASS_NAME,
            T_FUNCTION        => $twoBehind->is([T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC]) ? T_METHOD_NAME : T_FUNCTION_DECL,
            T_OBJECT_OPERATOR => $ahead->text === '(' ? T_METHOD_NAME : T_VARIABLE,
            T_DOUBLE_COLON    => $ahead->text === '(' || $ahead->id === T_INSTEADOF || $ahead->id === T_AS ? T_METHOD_NAME : T_CONST_NAME,
            T_AS              => T_METHOD_NAME,
            default           => (function () use ($behind, $ahead, $twoBehind, $tokens, $index) {
                if ($behind->is([':', '|', '?', ','])) {
                    return T_CLASS_NAME;
                }

                if ($ahead->text === '(') {
                    return T_FUNCTION_NAME;
                }

                if ($twoBehind->is(T_DECLARE)) {
                    return T_DECLARE_PARAMETER;
                }

                // public function foo(Bar $bar)
                // 6     54       32  0
                // This is where the -4 comes from
                if ($this->read($tokens, $index - 4, ltr: false)->is(T_FUNCTION)) {
                    return T_CLASS_NAME;
                }

                return T_CONST_NAME;
            }
            )()
        };
    }

    /**
     * Finds the nearest non-whitespace token at a given index in a given direction (either ltr or rtl).
     *
     * @param array<int, PhpToken> $tokens
     */
    protected function read(array $tokens, int $index, bool $ltr = true): PhpToken
    {
        $token = $tokens[$index];

        if ($token->id === T_WHITESPACE) {
            $token = $tokens[$index + ($ltr ? 1 : -1)];
        }

        return $token;
    }
}
