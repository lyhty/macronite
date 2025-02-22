<?php

namespace Lyhty\Macronite\Commands;

use Illuminate\Console\Command;
use Laravel\SerializableClosure\Support\ReflectionClosure;
use Lyhty\Macronite\MacroServiceProvider as Provider;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Terminal;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\select;

class MacroListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'macro:list
        {--a|all : List all macros from all providers}
        {--M|macroable= : Filter macros by the macroable class}
        {--P|provider= : Filter macros by the provider}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all defined application macros';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The terminal width resolver callback.
     *
     * @var \Closure|null
     */
    protected static $terminalWidthResolver;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->newLine();

        /** @var \Illuminate\Support\Collection<Provider> $providers */
        $providers = collect($this->laravel->getProviders(Provider::class))
            ->values()
            ->mapWithKeys(fn ($provider) => [get_class($provider) => $provider])
            ->sortKeys();

        if ($this->option('all') || $providers->containsOneItem()) {
            $select = 'all';
        } elseif ($this->option('provider')) {
            $select = str_replace('/', '\\', $this->option('provider'));

            if (! isset($providers[$select])) {
                alert("Provider [{$select}] not found");

                return Command::FAILURE;
            }
        } else {
            $select = select(
                "Which provider's macros would you like to see?",
                $providers->map(fn ($provider) => get_class($provider))->prepend('All', 'all'),
                scroll: 15,
            );
        }

        if ($select === 'all') {
            $mappedMacros = collect($this->mapAllMacros($providers));
        } else {
            $provider = $providers[$select];
            $mappedMacros = collect($provider->mapMacros());
        }

        if ($this->option('macroable')) {
            $macroableOpt = str_replace('/', '\\', $this->option('macroable'));
            $exactMatch = false;

            if (isset($mappedMacros[$macroableOpt])) {
                $mappedMacros = collect([$macroableOpt => $mappedMacros[$macroableOpt]]);
                $exactMatch = true;
            } else {
                $mappedMacros = $mappedMacros->filter(fn ($macros, $macroable) => str_contains($macroable, $macroableOpt));
            }
        }

        $terminalWidth = $this->getTerminalWidth();

        foreach ($mappedMacros as $macroable => $macros) {
            if (mb_strlen($macroable) >= $terminalWidth) {
                $macroable = '…'.strrev(substr(strrev($macroable), 0, $terminalWidth - 5));
            }

            if (isset($macroableOpt) && (! isset($exactMatch) || $exactMatch === false)) {
                $macroable = str_replace('\\', '/', str_replace(
                    $macroableOpt,
                    "<fg=yellow;options=underscore>{$macroableOpt}</>",
                    $macroable
                ));
            }

            $this->line("  <fg=yellow>{$macroable}</>");

            foreach ($macros as $name => $macro) {
                $spaces = 8;
                $dots = str_repeat('.', max(
                    $terminalWidth - mb_strlen($name.$macro) - $spaces,
                    0
                ));

                if (mb_strlen($name.$macro.$dots) + $spaces > $terminalWidth) {
                    $macro = '…'.strrev(substr(strrev($macro), 0, $terminalWidth - 1 - mb_strlen($name.$dots) - $spaces));
                }

                $this->line("  <fg=#6C7280>⇂</> {$name} <fg=#6C7280>{$dots} {$macro}</>");

                if ($this->output->isVerbose()) {
                    $refl = new ReflectionClosure((new $macro)());

                    foreach ($refl->getParameters() as $param) {
                        $this->line("    <fg=#6C7280>⇂ {$param}</>");
                    }

                    $returnType = $refl->hasReturnType() ? $refl->getReturnType() : 'mixed';
                    $this->line("    <fg=#6C7280>⇂ Return [ {$returnType} ]</>");
                }
            }
        }

        $this->line('');
        $this->line($this->determineMacroCountOutput($mappedMacros, $terminalWidth));
    }

    protected function mapAllMacros($providers)
    {
        $flattened = [];

        $providers->each(function (Provider $provider) use (&$flattened) {
            collect($provider->mapMacros())->each(function ($macros, $macroable) use (&$flattened) {
                $flattened[$macroable] = isset($flattened[$macroable])
                    ? array_merge($flattened[$macroable], $macros)
                    : $macros;

                ksort($flattened[$macroable]);
            });
        });

        return $flattened;
    }

    /**
     * Determine and return the output for displaying the number of routes in the CLI output.
     *
     * @param  \Illuminate\Support\Collection  $macros
     * @param  int  $terminalWidth
     * @return string
     */
    protected function determineMacroCountOutput($macros, $terminalWidth)
    {
        $count = $macros->flatten(1)->count();
        $macroCountText = "Showing [{$count}] macros";

        $offset = $terminalWidth - mb_strlen($macroCountText) - 2;

        $spaces = str_repeat(' ', $offset);

        return $spaces."<fg=blue;options=bold>$macroCountText</>";
    }

    /**
     * Set a callback that should be used when resolving the terminal width.
     *
     * @param  \Closure|null  $resolver
     * @return void
     */
    public static function resolveTerminalWidthUsing($resolver)
    {
        static::$terminalWidthResolver = $resolver;
    }

    /**
     * Get the terminal width.
     *
     * @return int
     */
    public static function getTerminalWidth()
    {
        return is_null(static::$terminalWidthResolver)
            ? (new Terminal)->getWidth()
            : call_user_func(static::$terminalWidthResolver);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['all', null, InputOption::VALUE_NONE, 'List all macros from all providers'],
            ['provider', null, InputOption::VALUE_OPTIONAL, 'Filter the macros by provider'],
        ];
    }
}
