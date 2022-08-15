<?php

namespace Lyhty\Macronite\Tests\Feature;

use Illuminate\Support\Arr;
use Lyhty\Macronite\Macronite;
use Lyhty\Macronite\Tests\TestCase;
use Lyhty\Macronite\Tests\TestMacroServiceProvider;

class MacroCachingTest extends TestCase
{
    protected ?TestMacroServiceProvider $provider = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->app->resolveProvider(TestMacroServiceProvider::class);
    }

    public function testCacheCommand(): void
    {
        $this->artisan('macro:cache')
            ->expectsOutput('Cached macros cleared!')
            ->expectsOutput('Macros cached successfully!')
            ->assertSuccessful();

        $this->assertTrue(Macronite::cacheExists($this->provider));
        $this->assertIsArray($macros = Macronite::getCachedProviderMacros($this->provider));
        $this->assertArrayHasKey(Arr::class, $macros);
    }

    public function testCacheClearCommand(): void
    {
        $this->artisan('macro:clear')
            ->expectsOutput('Cached macros cleared!')
            ->assertSuccessful();

        $this->assertFalse(Macronite::cacheExists($this->provider));
    }
}
