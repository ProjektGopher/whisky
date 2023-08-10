<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\FileJson;
use ProjektGopher\Whisky\Platform;

class Validate extends Command
{
    protected $signature = 'validate';

    protected $description = 'Verify that Whisky config is valid.';

    public function handle(): int
    {
        if (File::missing(Platform::cwd('whisky.json'))) {
            $this->error('Whisky has not been initialized in this project, aborting...');
            $this->line('Run `./vendor/bin/whisky install` to initialize Whisky in this project.');

            return Command::FAILURE;
        }

        FileJson::make(Platform::cwd('whisky.json'))->read();

        $this->info('Whisky config is valid.');

        return Command::SUCCESS;
    }
}
