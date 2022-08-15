<?php

namespace Lyhty\Macronite\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Lyhty\Macronite\MacroServiceProvider;

class MacroGenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'macro:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the missing macros based on registration';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $providers = array_filter(
            $this->laravel->getProviders(MacroServiceProvider::class),
            fn ($provider) => Str::startsWith(get_class($provider), $this->laravel->getNamespace())
        );

        foreach ($providers as $provider) {
            foreach ($provider->getMacros() as $class => $macros) {
                $this->makeMacros($class, $macros);
            }
        }

        $this->info('Macros generated successfully!');

        return Command::SUCCESS;
    }

    /**
     * Make the event and listeners for the given event.
     *
     * @param  string  $event
     * @param  array  $listeners
     * @return void
     */
    protected function makeMacros($mixin, $macros)
    {
        if (! Str::contains($mixin, '\\')) {
            return;
        }

        foreach ($macros as $macro) {
            $this->callSilent('make:macro', ['name' => $macro, '--mixin' => $mixin]);
        }
    }
}
