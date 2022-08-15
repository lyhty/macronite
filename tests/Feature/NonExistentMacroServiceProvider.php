<?php

namespace Lyhty\Macronite\Tests\Feature;

use Lyhty\Macronite\MacroServiceProvider;

class NonExistentMacroServiceProvider extends MacroServiceProvider
{
    protected static array $macros = [
        \Illuminate\Support\Arr::class => [
            \App\Macros\Arr\NonExistentMacro::class,
            'example' => \App\Macros\Arr\AnotherNonExistentMacro::class,
            'anotherExample' => \App\Macros\Arr\YetAnotherNonExistentMacro::class,
        ]
    ];
}
