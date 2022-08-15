<?php

namespace Lyhty\Macronite\Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Lyhty\Macronite\Tests\Feature\NonExistentMacroServiceProvider as Provider;
use Lyhty\Macronite\Tests\TestCase;

class MacroGenerateTest extends TestCase
{
    protected ?Provider $provider = null;

    public function testMacroGeneration(): void
    {
        $this->artisan('macro:generate --namespace=Lyhty/Macronite/Tests/Feature')
            ->expectsOutput('Macros generated successfully!')
            ->assertSuccessful();

        foreach ($this->getGeneratedMacroFiles() as $filename) {
            $this->assertFileExists($filename);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->app->resolveProvider(Provider::class);
        $this->unlinkGeneratedMacros();
    }

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [Provider::class]);
    }

    protected function tearDown(): void
    {
        $this->unlinkGeneratedMacros();
        parent::tearDown();
    }

    private function unlinkGeneratedMacros()
    {
        foreach ($this->getGeneratedMacroFiles() as $filename) {
            if (is_file($filename)) {
                unlink($filename);
            }
        }
    }

    private function getGeneratedMacroFiles(): array
    {
        return array_map(fn ($macro) => $this->app->basePath(
            lcfirst(Str::of($macro)->replace('\\', '/')->finish('.php'))
        ), $this->provider->getMacros()[Arr::class]);
    }
}
