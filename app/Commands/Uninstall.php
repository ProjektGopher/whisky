<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Hook;
use ProjektGopher\Whisky\Platform;

class Uninstall extends Command
{
    protected $signature = 'uninstall {--json}';

    protected $description = 'Uninstall git hooks';

    public function handle(): int
    {
        Hook::all(Hook::FROM_GIT)->each(function (Hook $hook): void {
            if (! $hook->uninstall()) {
                return;
            }
            $this->info("Removed Whisky from {$hook->name} hook.");
        });

        if (
            $this->option('json') ||
            $this->confirm('Would you also like to remove whisky.json?')
        ) {
            File::delete(Platform::cwd('whisky.json'));
            $this->info('whisky.json removed.');
        }

        return Command::SUCCESS;
    }
}
