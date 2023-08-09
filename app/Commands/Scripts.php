<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Hook;
use ProjektGopher\Whisky\Platform;

class Scripts extends Command
{
    protected $signature = 'scripts {hook}';

    protected $description = 'Get a list of terminal commands for a given hook';

    public function handle(): int
    {
        if (File::missing(Platform::cwd('whisky.json'))) {
            $this->error('Whisky has not been initialized in this project, aborting...');
            $this->line('Run `./vendor/bin/whisky install` to initialize Whisky in this project.');

            return Command::FAILURE;
        }

        Hook::make($this->argument('hook'))->getScripts()->each(function (string $script): void {
            $this->line($script);
        });

        return Command::SUCCESS;
    }
}
