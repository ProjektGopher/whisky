<?php

namespace ProjektGopher\Whisky\Commands;

use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Hook;
use ProjektGopher\Whisky\Platform;

/**
 * TODO: This has a lot of duplication wrt Commands\Uninstall and Commands\Install.
 */
class Update extends Command
{
    protected $signature = 'update';

    protected $description = 'Update git hooks';

    public function __construct(
        public Platform $platform,
    ) {
        parent::__construct();
    }

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
                $this->info("Ensuring {$hook->name} hook is executable...");
            }
            $hook->ensureExecutable();

            if ($this->option('verbose')) {
                $this->info("{$hook->name} git hook installed successfully.");
            }
        });

        $this->line('Git hooks updated successfully.');

        return Command::SUCCESS;
    }
}
