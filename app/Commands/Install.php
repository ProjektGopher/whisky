<?php

namespace Whisky\Commands;

use Whisky\Whisky;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Phar;

class Install extends Command
{
    protected $signature = 'install';

    protected $description = 'Install git hooks';

    public function handle(): int
    {
        if (! $this->gitIsInitialized()) {
            $this->error('Git has not been initialized in this project, aborting...');

            return Command::FAILURE;
        }

        if (
            ! File::exists(Whisky::cwd('whisky.json')) ||
            $this->ask('overwrite existing whisky.json?', 'no')
        ) {
            $this->info('Creating whisky.json in project root...');
            File::put(
                Whisky::cwd('whisky.json'),
                File::get(Whisky::base_path('stubs/whisky.json')),
            );
        }

        if ($this->option('verbose')) {
            $this->info('Installing git hooks...');
        }

        $this->getHooks()->each(function ($hook) {
            $this->installHook($hook);
        });

        if ($this->option('verbose')) {
            $this->info('Verifying hooks are executable...');
        }
        exec('chmod +x '.Whisky::cwd('.git/hooks').'/*');
        exec('chmod +x '.Whisky::base_path('bin/run-hook'));

        $this->info('Git hooks installed successfully.');

        return Command::SUCCESS;
    }

    private function gitIsInitialized(): bool
    {
        return File::exists(Whisky::cwd('.git'));
    }

    private function getHooks(): Collection
    {
        return collect(array_keys(Whisky::readConfig('hooks')));
    }

    private function hookIsInstalled(string $hook): bool
    {
        return Str::contains(
            File::get(Whisky::cwd(".git/hooks/{$hook}")),
            "eval \"$(./vendor/bin/whisky get-run-cmd {$hook})\"",
        );
    }

    private function installHook(string $hook): void
    {
        if (! File::exists(Whisky::cwd(".git/hooks/{$hook}"))) {
            if ($this->option('verbose')) {
                $this->line("{$hook} file does not exist yet, creating...");
            }
            File::put(Whisky::cwd(".git/hooks/{$hook}"), '#!/bin/sh'.PHP_EOL);
        }

        if ($this->hookIsInstalled($hook)) {
            if ($this->option('verbose')) {
                $this->warn("{$hook} git hook already installed, skipping...");
            }

            return;
        }

        File::append(
            Whisky::cwd(".git/hooks/{$hook}"),
            "eval \"$(./vendor/bin/whisky get-run-cmd {$hook})\"".PHP_EOL,
        );

        if ($this->option('verbose')) {
            $this->info("{$hook} git hook installed successfully.");
        }
    }
}
