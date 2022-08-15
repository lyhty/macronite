<?php

namespace Lyhty\Macronite\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Lyhty\Macronite\MacroServiceProvider;
use ReflectionClass;

class MacroGenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'macro:generate {--N|namespace= : Discover macros from providers in given namespace}';

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
        foreach ($this->getProvidersInNamespace() as $provider) {
            foreach ($provider->getMacros() as $macroable => $macros) {
                $this->makeMacros($macroable, $macros);
            }
        }

        $this->info('Macros generated successfully!');

        return Command::SUCCESS;
    }

    /**
     * Return the provider instances that match the namespace.
     *
     * @return \Lyhty\Macronite\MacroServiceProvider[]
     */
    protected function getProvidersInNamespace()
    {
        $namespace = Str::of($this->option('namespace') ?: $this->laravel->getNamespace())
            ->replace('/', '\\')
            ->finish('\\');

        return array_filter(
            $this->laravel->getProviders(MacroServiceProvider::class),
            fn ($provider) => Str::startsWith(
                Str::finish((new ReflectionClass($provider))->getNamespaceName(), '\\'),
                $namespace
            )
        );
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
