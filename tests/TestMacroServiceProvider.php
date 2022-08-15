<?php

namespace Lyhty\Macronite\Tests;

use Closure;
use Lyhty\Macronite\MacroServiceProvider;

class TestMacroServiceProvider extends MacroServiceProvider
{
    protected static array $macros = [
        \Illuminate\Support\Arr::class => [
            ExampleMacro::class,
        ],
        \Illuminate\Support\Str::class => [
            'anotherName' => ExampleMacro::class,
        ],
        \Illuminate\Support\Collection::class => [
            ExampleWithConstMacro::class,
        ],
    ];
}

class ExampleMacro
{
    public function __invoke(): Closure
    {
        return function (string $value) {
            return 'foo:'.$value;
        };
    }
}

class ExampleWithConstMacro extends ExampleMacro
{
    const MACRO_NAME = 'yetAnotherName';
}
