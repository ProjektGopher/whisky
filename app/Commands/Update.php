<?php

namespace ProjektGopher\Whisky\Commands;

use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Hook;

/**
 * TODO: This has a lot of duplication wrt Commands\Uninstall and Commands\Install.
 */
class Update extends Command
{
    protected $signature = 'update';

    protected $description = 'Update git hooks';

    public function handle(): int
    {
        Hook::all(Hook::FROM_GIT)->each(function (Hook $hook): void {
            if (! $hook->uninstall()) {
                return;
            }

            if ($this->option('verbose')) {
                $this->info("Removed Whisky from {$hook->name} hook.");
            }
        });

        Hook::all(Hook::FROM_CONFIG)->each(function (Hook $hook) {
            if ($hook->fileIsMissing()) {
                if ($this->option('verbose')) {
                    $this->line("{$hook->name} file does not exist yet, creating...");
                }
                $hook->enable();
            }

            $hook->install();

            if ($this->option('verbose')) {
                $this->info("{$hook->name} git hook installed successfully.");
            }
        });

        $this->line('done.');

        return Command::SUCCESS;
    }
}
