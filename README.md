<p>
  <img src="https://matti.suoraniemi.com/storage/lyhty-macronite.png" width="400">
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lyhty/macronite.svg?label=&logo=packagist&logoColor=white&style=flat-square)](https://packagist.org/packages/lyhty/macronite)
[![PHP](https://img.shields.io/packagist/php-v/lyhty/macronite?style=flat-square&label=&logo=php&logoColor=white)](https://packagist.org/packages/lyhty/macronite)
[![Laravel](https://img.shields.io/static/v1?label=&message=^8.0%20...%20^10.0&color=red&style=flat-square&logo=laravel&logoColor=white)](https://packagist.org/packages/lyhty/macronite)
[![Total Downloads](https://img.shields.io/packagist/dt/lyhty/macronite.svg?style=flat-square)](https://packagist.org/packages/lyhty/macronite)
[![Tests](https://img.shields.io/github/workflow/status/lyhty/macronite/Run%20tests?style=flat-square)](https://github.com/lyhty/macronite/actions/workflows/php.yml)
[![StyleCI](https://github.styleci.io/repos/523255216/shield)](https://github.styleci.io/repos/523255216)
[![License](https://img.shields.io/packagist/l/lyhty/macronite.svg?style=flat-square)](https://packagist.org/packages/lyhty/macronite)

<!-- CUTOFF -->

This package provides a very convenient macro maker to your Laravel project.

## Installation

Install the package with Composer:

    composer require lyhty/macronite

The package registers itself automatically.

## Extending the MacroServiceProvider

You can make your own pretty MacroServiceProviders by extending the MacroServiceProvider class
provided by this package!

Here's an example:

```php
<?php

namespace App\Providers;

use Lyhty\Macronite\MacroServiceProvider as ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    protected static array $macros = [
        \Illuminate\Support\Collection::class => [
            'example' => \App\Macros\ExampleMacro::class,
            \App\Macros\SecondExampleMacro::class,
        ]
    ];
}
```

The first macro class referenced in the example above would look something like:

```php
<?php

namespace App\Macros;

class ExampleMacro
{
    public function __invoke(): \Closure
    {
        return function () {
            // Something cool worth getting macroed happens here...
            return $this;
        }
    }
}
```

The Macro service provider handles the mapping of the macros quite responsively. You can either
explicitly name the macro by setting the key of the array row defining the macro as the name you wish
to use. Alternatively you can define a constant `MACRO_NAME` inside the macro file, which will be used
if the key is not defined in the service provider.

Finally, if the key is not defined in the service provider and the macro class does not contain
the constant, the class name will be used. For example `ExampleMacro::class` macro will be named as
`example`.

## Commands

### `make:macro`

You can use `make:macro <name>` to generate a macro file to help you with the structure. The command
supports `--mixin` option (example: `--mixin=/Illuminate/Support/Collection`). This will add a PHP
docblock above the macro class declaration with `@mixin` tag.

### `macro:generate`

The package also comes with `macro:generate` command. If you have a provider class setup that extends
`Lyhty\Macronite\MacroServiceProvider` class, the command will go through the macros
defined in the provider and generate the ones that are missing. This way you can define multiple macros
you know you will have and then generate them in bulk. It is very similar to Laravel's `event:generate`
in its behavior.

For example:

```php
<?php

namespace App\Providers;

use Lyhty\Macronite\MacroServiceProvider as ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    protected static array $macros = [
        \Illuminate\Support\Collection::class => [
            'example' => \App\Macros\ExampleMacro::class,
        ]
    ];
}
```

Assuming `ExampleMacro` doesn't exist yet, the command would then generate the macro class, automatically
also filling in the `@mixin` tag.

### `macro:cache` & `macro:clear`

Since the macro mapping is very dynamic, you can cache the macros to be set in stone with `macro:cache`.
The cache can be cleared with the `macro:clear` function.

## License

Lyhty Macros is open-sourced software licensed under the [MIT license](LICENSE.md).
