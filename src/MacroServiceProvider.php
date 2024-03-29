<?php

namespace Lyhty\Macronite;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

abstract class MacroServiceProvider extends ServiceProvider
{
    /**
     * The macro mappings for the application.
     */
    protected static array $macros = [];

    /**
     * Bootstrap closure based application macros.
     */
    public function bootMacros(): void
    {
        //
    }

    /**
     * Return the macros mappings array.
     */
    public function getMacros(): array
    {
        return static::$macros;
    }

    /**
     * Map the macros to the final format.
     */
    public function mapMacros(): array
    {
        $list = $this->getMacros();

        foreach ($list as $macroable => $macros) {
            $list[$macroable] = collect($macros)
                ->mapWithKeys(fn ($class, $key) => [
                    Macronite::resolveMacroName($key, $class) => $class,
                ])
                ->filter(fn ($class) => class_exists($class))
                ->all();
        }

        return $list;
    }

    /**
     * Bootstrap application macros.
     *
     * @return void
     */
    public function boot()
    {
        $macros = Macronite::getCachedProviderMacros($this) ?: $this->mapMacros();

        foreach ($macros as $macroable => $values) {
            collect($values)
                ->when(
                    method_exists($this, $method = 'filterMacros'),
                    fn (Collection $collection): Collection => $this->{$method}($collection, $macroable)
                )
                ->reject(fn ($class, $macro) => Macronite::alreadyMacroed($macroable, $macro))
                ->each(fn ($class, $macro) => $macroable::macro($macro, app($class)()));
        }

        $this->bootMacros();
    }
}
