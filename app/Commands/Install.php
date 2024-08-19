<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Hook;
use ProjektGopher\Whisky\Platform;
use ProjektGopher\Whisky\Whisky;

class Install extends Command
{
    protected $signature = 'install';

    protected $description = 'Install git hooks';

    public function __construct(
        public Platform $platform,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! Platform::gitIsInitialized()) {
            $this->info(Platform::getGitDir());
            $this->error('Git has not been initialized in this project, aborting...');

            return Command::FAILURE;
        }

        if (
            ! File::exists(Platform::cwd('whisky.json')) ||
            $this->confirm('overwrite existing whisky.json?', false)
        ) {
            $this->info('Creating whisky.json in project root...');
            File::put(
                Platform::cwd('whisky.json'),
                File::get(Whisky::base_path('stubs/whisky.json')),
            );
        }

        if ($this->option('verbose')) {
            $this->info('Installing git hooks...');
        }

        /**
         * Do the hook installation, and talk about it.
         */
        Hook::each(function (Hook $hook): void {
            if ($hook->isNotEnabled()) {
                if ($this->option('verbose')) {
                    $this->line("{$hook->name} file does not exist yet, creating...");
                }
                $hook->enable();
            }

            if ($hook->isInstalled()) {
                if ($this->option('verbose')) {
                    $this->warn("{$hook->name} git hook already installed, skipping...");
                }

                return;
            }

            $hook->install();

            if ($this->option('verbose')) {
                $this->info("{$hook->name} git hook installed successfully.");
            }
        });

        if ($this->platform->isNotWindows()) {
            if ($this->option('verbose')) {
                $this->info('Verifying hooks are executable...');
            }
            exec('chmod +x '.Platform::getGitDir('hooks').'/*');
            exec('chmod +x '.Whisky::base_path('bin/run-hook'));
        }

        $this->info('Git hooks installed successfully.');

        return Command::SUCCESS;
    }
}
