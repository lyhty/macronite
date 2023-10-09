<?php

namespace Lyhty\Macronite\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Lyhty\Macronite\Macronite;

class MacroClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'macro:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all cached macros';

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
     *
     * @throws \RuntimeException
     */
    public function handle()
    {
        $this->files->deleteDirectory(Macronite::getCacheFolderPath());

        $this->info('Cached macros cleared!');

        return Command::SUCCESS;
    }
}
