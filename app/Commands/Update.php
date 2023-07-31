<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Whisky;
use SplFileInfo;

/**
 * TODO: This command has a lot of duplication.
 * Most of this can live on the Whisky class,
 * and some should be in a new Hook class.
 */
class Update extends Command
{
    protected $signature = 'update';

    protected $description = 'Update git hooks';

    public function handle(): int
    {

        $this->uninstall();

        $this->getHooks()->each(function (string $hook) {
            $this->installHook($hook);
        });

        $this->line('done.');

        return Command::SUCCESS;
    }

    private function uninstall(): void
    {
        collect(
            File::files(Whisky::cwd('.git/hooks'))
        )->filter(
            fn (SplFileInfo $file) => ! str_ends_with($file->getFilename(), 'sample')
        )->each(function (SplFileInfo $file): void {
            $path = $file->getPathname();
            $hook = $file->getFilename();
            $contents = File::get($path);
            $bin = Whisky::bin();
            $command = "eval \"$({$bin} get-run-cmd {$hook})\"".PHP_EOL;

            if (! str_contains($contents, $command)) {
                return;
            }

            $contents = str_replace(
                $command,
                '',
                File::get($path),
            );
            File::put($path, $contents);
            $this->info("Removed Whisky from {$hook} hook.");
        });
    }

    private function getHooks(): Collection
    {
        return collect(array_keys(Whisky::readConfig('hooks')));
    }

    private function hookIsInstalled(string $hook): bool
    {
        $bin = Whisky::bin();

        return Str::contains(
            File::get(Whisky::cwd(".git/hooks/{$hook}")),
            "eval \"$({$bin} get-run-cmd {$hook})\"",
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

        $bin = Whisky::bin();
        File::append(
            Whisky::cwd(".git/hooks/{$hook}"),
            "eval \"$({$bin} get-run-cmd {$hook})\"".PHP_EOL,
        );

        if ($this->option('verbose')) {
            $this->info("{$hook} git hook installed successfully.");
        }
    }
}
