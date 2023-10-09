<?php

namespace Lyhty\Macronite\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Lyhty\Macronite\Macronite;
use Lyhty\Macronite\MacroServiceProvider;

class MacroCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'macro:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Discover and cache the application's macros";

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new config clear command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('macro:clear');

        if (! $this->files->isDirectory(Macronite::getCacheFolderPath())) {
            $this->files->makeDirectory(Macronite::getCacheFolderPath());
        }

        /** @var array<MacroServiceProvider> $providers */
        $providers = $this->laravel->getProviders(MacroServiceProvider::class);

        foreach ($providers as $provider) {
            $macros = $provider->mapMacros();

            file_put_contents(
                Macronite::getProviderCachePath($provider),
                '<?php return '.var_export($macros, true).';'
            );
        }

        $this->info('Macros cached successfully!');

        return Command::SUCCESS;
    }
}
