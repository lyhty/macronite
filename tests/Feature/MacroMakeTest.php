<?php

namespace Lyhty\Macronite\Tests\Feature;

use Lyhty\Macronite\Tests\TestCase;

class MacroMakeTest extends TestCase
{
    protected static array $macroPaths = [
        'arr' => 'app/Macros/Arr/TestMacro.php',
        'str' => 'app/Macros/Str/TestMacro.php',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->clearMacros();
    }

    public function testMacroMakeCommand(): void
    {
        $this->artisan('make:macro Arr/TestMacro')
            ->expectsOutput('Macro created successfully.')
            ->assertSuccessful();

        $this->assertFileExists($this->app->basePath(static::$macroPaths['arr']));
    }

    public function testMacroMakeWithMixinArgumentCommand(): void
    {
        $this->artisan('make:macro Str/TestMacro --mixin=/Illuminate/Support/Str')
            ->expectsOutput('Macro created successfully.')
            ->assertSuccessful();

        $this->assertFileExists($this->app->basePath(static::$macroPaths['str']));

        $this->assertStringContainsString(
            "/**\n * @mixin \Illuminate\Support\Str\n */\r\nclass TestMacro",
            file_get_contents($this->app->basePath(static::$macroPaths['str']))
        );
    }

    protected function clearMacros(): void
    {
        foreach (static::$macroPaths as $path) {
            $fullPath = $this->app->basePath($path);

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    protected function tearDown(): void
    {
        $this->clearMacros();
        parent::tearDown();
    }
}
