<?php

namespace Felix\Tin;

use PhpToken;

class Token extends PhpToken
{
    public function getTokenName(): string
    {
        if ($this->id < 256) {
            return chr($this->id);
        }

        return match ($this->id) {
            T_CLASS_NAME        => 'T_CLASS_NAME',
            T_FUNCTION_NAME     => 'T_FUNCTION_NAME',
            T_CONST_NAME        => 'T_CONST_NAME',
            T_BUILTIN_TYPE      => 'T_BUILTIN_TYPE',
            T_METHOD_NAME       => 'T_METHOD_NAME',
            T_FUNCTION_DECL     => 'T_FUNCTION_DECL',
            T_DECLARE_PARAMETER => 'T_DECLARE_PARAMETER',
            T_ATTRIBUTE_CLASS   => 'T_ATTRIBUTE_CLASS',
            default             => parent::getTokenName()
        };
    }
}
