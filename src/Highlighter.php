<?php

namespace Felix\Highlighter;

use Felix\Highlighter\Contracts\Theme;

class Highlighter
{
    public static array $keywords = [
        T_ABSTRACT,
        T_ARRAY,
        T_FOREACH,
        T_AS,
        T_ECHO,
        T_TRY,
        T_PRINT,
        T_CATCH,
        T_CLONE,
        T_CLOSE_TAG,
        T_SWITCH,
        T_CASE,
        T_BREAK,
        T_DEFAULT,
        T_OPEN_TAG,
        T_OPEN_TAG_WITH_ECHO,
        T_CLASS,
        T_PROTECTED,
        T_PUBLIC,
        T_PRIVATE,
        T_FUNCTION,
        T_NEW,
        T_RETURN,
        T_FN,
        T_CONST,
        T_CONTINUE,
        T_DO,
        T_ELSE,
        T_IF,
        T_ELSEIF,
        T_EMPTY,
        T_WHILE,
        T_ENDDECLARE,
        T_ENDFOR,
        T_ENDFOREACH,
        T_ENDIF,
        T_ENDSWITCH,
        T_ENDWHILE,
        T_START_HEREDOC,
        T_END_HEREDOC,
        T_EXIT,
        T_EVAL,
        T_EXTENDS,
        T_FINALLY,
        T_FINAL,
        T_FOR,
        T_GLOBAL,
        T_GOTO,
        T_HALT_COMPILER,
        T_IMPLEMENTS,
        T_INCLUDE,
        T_REQUIRE,
        T_INSTANCEOF,
        T_INSTEADOF,
        T_INTERFACE,
        T_ISSET,
        T_LIST,
        T_LOGICAL_AND,
        T_LOGICAL_XOR,
        T_LOGICAL_OR,
        T_NAMESPACE,
        T_PRINT,
        T_REQUIRE_ONCE,
        T_INCLUDE_ONCE,
        T_STATIC,
        T_THROW,
        T_TRAIT,
        T_UNSET,
        T_USE,
        T_VAR,
        T_YIELD,
        T_YIELD_FROM,
        T_MATCH,
        T_FN,
        T_DECLARE,

        // Custom
        T_BUILTIN_TYPE,
        T_TRUE,
        T_FALSE,
        T_NULL,
    ];
    public static array $constants = [
        T_CLASS_C,
        T_METHOD_C,
        T_NS_C,
        T_FUNC_C,
        T_TRAIT_C,
        T_DIR,
        T_FILE,
        T_LINE,
    ];
    public static array $casts = [
        T_BOOL_CAST,
        T_ARRAY_CAST,
        T_DOUBLE_CAST,
        T_INT_CAST,
        T_UNSET_CAST,
        T_OBJECT_CAST,
        T_STRING_CAST,
    ];
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
        $reader      = new Reader($code);

        while (!$reader->eof()) {
            $token = $reader->consume();

            if (in_array($token[0], static::$keywords)) {
                $color = $this->theme->keyword();
            } elseif (in_array($token[0], static::$constants)) {
                $color = $this->theme->variable();
            } elseif (in_array($token[0], static::$casts)) {
                $color = $this->theme->keyword();
            } else {
                $color = match ($token[0]) {
                    T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE => $this->theme->string(),
                    T_COMMENT, T_DOC_COMMENT => $this->theme->comment(),
                    T_VARIABLE, T_CONST_NAME, T_NUM_STRING => $this->theme->variable(),
                    T_LNUMBER, T_DNUMBER => $this->theme->number(),
                    T_METHOD_NAME => $this->theme->function(),
                    default       => $this->theme->default()
                };
            }

            [$r, $g, $b] = array_map('hexdec', str_split($color, 2));

            $highlighted .= sprintf("\e[38;2;%s;%s;%sm%s\e[0m", $r, $g, $b, $token[1]);
        }

        return $highlighted;
    }
}
