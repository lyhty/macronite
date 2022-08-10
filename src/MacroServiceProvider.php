<?php

namespace Lyhty\Macronite;

use Illuminate\Support\ServiceProvider;

abstract class MacroServiceProvider extends ServiceProvider
{
    /**
     * The macro mappings for the application.
     *
     * @var array
     */
    protected static array $macros = [];

    /**
     * Bootstrap closure based application macros.
     *
     * @return void
     */
    public function bootMacros(): void
    {
        //
    }

    /**
     * Return the macros mappings array.
     *
     * @return array
     */
    public function getMacros(): array
    {
        return static::$macros;
    }

    /**
     * Map the macros to the final format.
     *
     * @return array
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
                ->reject(fn ($class, $macro) => Macronite::alreadyMacroed($macroable, $macro))
                ->each(fn ($class, $macro) => $macroable::macro($macro, app($class)()));
        }

        static::bootMacros();
    }
}
