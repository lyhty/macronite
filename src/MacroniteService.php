<?php

namespace Lyhty\Macronite;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MacroniteService
{
    protected array $config;

    protected Filesystem $files;

    /**
     * The macronite-service constructor.
     *
     * @param  array  $config
     */
    public function __construct(array $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files = $files;
    }

    /**
     * Get the full path of the cache folder.
     *
     * @return string
     */
    public function getCacheFolderPath(): string
    {
        return app()->bootstrapPath($this->config['cache_folder']);
    }

    /**
     * Get the filename for the given provider's macro cache.
     *
     * @param  MacroServiceProvider  $provider
     * @return string
     */
    public function getProviderCacheFilename(MacroServiceProvider $provider): string
    {
        return Str::of(get_class($provider))->replace('\\', '')->snake()->finish('.php');
    }

    /**
     * Get the full path for the given provider's macro cache.
     *
     * @param  MacroServiceProvider  $provider
     * @return string
     */
    public function getProviderCachePath(MacroServiceProvider $provider): string
    {
        return sprintf('%s/%s', $this->getCacheFolderPath(), $this->getProviderCacheFilename($provider));
    }

    /**
     * Return boolean value whether the given provider is cached.
     *
     * @param  MacroServiceProvider  $provider
     * @return bool
     */
    public function cacheExists(MacroServiceProvider $provider): bool
    {
        return $this->files->exists($this->getProviderCachePath($provider));
    }

    /**
     * Get the cached macros of the given provider.
     *
     * @param  MacroServiceProvider  $provider
     * @return array|null
     */
    public function getCachedProviderMacros(MacroServiceProvider $provider): ?array
    {
        return $this->cacheExists($provider)
            ? include $this->getProviderCachePath($provider)
            : null;
    }

    /**
     * Resolve macro name for the given macro class.
     *
     * @param  string|int|null  $key
     * @param  string  $class
     * @return string
     */
    public function resolveMacroName($key, $class): string
    {
        if (is_string($key)) {
            return $key;
        }

        return defined("$class::MACRO_NAME")
            ? constant("$class::MACRO_NAME")
            : Str::of($class)->classBasename()->before('Macro')->camel();
    }

    /**
     * Return boolean value whether given macro is already macroed for given Macroable.
     *
     * @param  object|string  $macroable
     * @param  string  $macro
     * @return bool
     */
    public static function alreadyMacroed($macroable, string $macro): bool
    {
        return static_method_exists($macroable, 'hasMacro')
            && $macroable::hasMacro($macro);
    }
}
