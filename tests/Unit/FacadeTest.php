<?php

namespace Lyhty\Macronite\Tests\Unit;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lyhty\Macronite\Macronite;
use Lyhty\Macronite\MacroniteService;
use Lyhty\Macronite\Tests\ExampleMacro;
use Lyhty\Macronite\Tests\ExampleWithConstMacro;
use Lyhty\Macronite\Tests\TestCase;
use Lyhty\Macronite\Tests\TestMacroServiceProvider;

class FacadeTest extends TestCase
{
    protected ?TestMacroServiceProvider $provider = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->app->resolveProvider(TestMacroServiceProvider::class);
    }

    public function testFacadeExistence(): void
    {
        $this->assertInstanceOf(MacroniteService::class, Macronite::getFacadeRoot());
    }

    public function testCacheFolderPath(): void
    {
        $path = Str::after(Macronite::getCacheFolderPath(), '/laravel');

        $this->assertSame('/bootstrap/cache/macronite', $path);
    }

    public function testFolderNameForServiceProvider(): void
    {
        $this->assertSame(
            'lyhty_macronite_tests_test_macro_service_provider.php',
            Macronite::getProviderCacheFilename($this->provider)
        );
    }

    public function testCachePathForServiceProvider(): void
    {
        $this->assertSame(
            '/bootstrap/cache/macronite/lyhty_macronite_tests_test_macro_service_provider.php',
            Str::after(Macronite::getProviderCachePath($this->provider), '/laravel')
        );
    }

    public function testCacheExists(): void
    {
        $this->assertFalse(Macronite::cacheExists($this->provider));
    }

    public function testGetCachedProviderMacros(): void
    {
        $this->assertNull(Macronite::getCachedProviderMacros($this->provider));
    }

    public function testResolveMacroName(): void
    {
        $this->assertSame('example', Macronite::resolveMacroName('example', ExampleMacro::class));
        $this->assertSame('example', Macronite::resolveMacroName(1, ExampleMacro::class));
        $this->assertSame('yetAnotherName', Macronite::resolveMacroName(1, ExampleWithConstMacro::class));
    }

    public function testAlreadyMacroed(): void
    {
        $this->assertTrue(Macronite::alreadyMacroed(Arr::class, 'example'));
        $this->assertTrue(Macronite::alreadyMacroed(Str::class, 'anotherName'));
        $this->assertTrue(Macronite::alreadyMacroed(Collection::class, 'yetAnotherName'));
    }
}
