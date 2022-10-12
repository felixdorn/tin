<?php

declare(strict_types=1);

namespace Felix\Tin\Enums;

enum TokenType: string
{
    case Keyword        = 'keyword';
    case Variable       = 'variable';
    case Comment        = 'comment';
    case DocComment     = 'docComment';
    case String         = 'string';
    case Function       = 'function';
    case Number         = 'number';
    case Attribute      = 'attribute';
    case NamedParameter = 'namedParameter';
    case Default        = 'default';
    case Html           = 'html';
    case LineNumber     = 'lineNumber';

    /** Converts a T_* constant to a TokenType, constants defined in src/constants.php are also supported */
    public static function fromId(int $id): self
    {
        // A token with an id lower than 256 is equal to ord($token->text)
        // and will never be colorized as it will be something like: ;{}()[]
        // See token_get_all() for more information.
        if ($id < 256) {
            return self::Default;
        }

        return match ($id) {
            T_METHOD_NAME, T_FUNCTION_DECL => TokenType::Function,
            T_COMMENT     => TokenType::Comment,
            T_DOC_COMMENT => TokenType::DocComment,
            T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE => TokenType::String,
            T_INLINE_HTML => TokenType::Html,
            T_LNUMBER, T_DNUMBER => TokenType::Number,
            T_VARIABLE, T_CONST_NAME, T_NUM_STRING, T_CLASS_C, T_METHOD_C, T_NS_C, T_FUNC_C, T_TRAIT_C, T_DIR, T_FILE, T_LINE => TokenType::Variable,
            T_ATTRIBUTE, T_ATTRIBUTE_CLASS, T_ATTRIBUTE_END => TokenType::Attribute,
            T_NAMED_PARAMETER => TokenType::NamedParameter,
            // all keywords
            T_ABSTRACT, T_ARRAY, T_FOREACH, T_AS, T_ECHO, T_TRY, T_CATCH, T_CLONE, T_CLOSE_TAG, T_SWITCH, T_CASE, T_BREAK,
            T_DEFAULT, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_CLASS, T_PROTECTED, T_PUBLIC, T_PRIVATE, T_FUNCTION, T_NEW,
            T_RETURN, T_CONST, T_CONTINUE, T_DO, T_ELSE, T_IF, T_ELSEIF, T_EMPTY, T_WHILE, T_ENDDECLARE, T_ENDFOR,
            T_ENDFOREACH, T_ENDIF, T_ENDSWITCH, T_ENDWHILE, T_START_HEREDOC, T_END_HEREDOC, T_EXIT, T_EVAL, T_EXTENDS,
            T_FINALLY, T_FINAL, T_FOR, T_GLOBAL, T_GOTO, T_HALT_COMPILER, T_IMPLEMENTS, T_INCLUDE, T_REQUIRE, T_INSTANCEOF,
            T_INSTEADOF, T_INTERFACE, T_ISSET, T_LIST, T_LOGICAL_AND, T_LOGICAL_XOR, T_LOGICAL_OR, T_NAMESPACE, T_PRINT,
            T_REQUIRE_ONCE, T_INCLUDE_ONCE, T_STATIC, T_THROW, T_TRAIT, T_UNSET, T_USE, T_VAR, T_YIELD, T_YIELD_FROM, T_MATCH,
            T_FN, T_DECLARE, T_BUILTIN_TYPE, T_BOOL_CAST, T_ARRAY_CAST, T_DOUBLE_CAST, T_INT_CAST, T_UNSET_CAST, T_OBJECT_CAST,
            T_ENUM, T_READONLY, T_STRING_CAST => TokenType::Keyword,
            default => TokenType::Default,
        };
    }
}
