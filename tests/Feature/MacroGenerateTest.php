<?php

namespace Lyhty\Macronite\Tests\Feature;

use Lyhty\Macronite\Tests\TestCase;

class MacroGenerateTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return array_merge(
            parent::getPackageProviders($app),
            [NonExistentMacroServiceProvider::class]
        );
    }

    public function testMacroGeneration(): void
    {
        $this->artisan('macro:generate')
            ->expectsOutput('Macros generated successfully!')
            ->assertSuccessful();
    }
}
