<?php

namespace Lyhty\Macronite;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getCacheFolderPath()
 * @method static string getProviderCacheFilename(MacroServiceProvider $provider)
 * @method static string getProviderCachePath(MacroServiceProvider $provider)
 * @method static bool cacheExists(MacroServiceProvider $provider)
 * @method static array|null getCachedProviderMacros(MacroServiceProvider $provider)
 * @method static string resolveMacroName($key, $class)
 * @method static bool alreadyMacroed($macroable, string $macro)
 */
class Macronite extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MacroniteService::class;
    }
}
