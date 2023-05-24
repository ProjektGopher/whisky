<?php

namespace App\Commands;

use App\Whisky;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

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
        
        $this->getHooks()->each(function ($hook) {
            $this->installHook($hook);
        });

        $this->line('done.');

        return Command::SUCCESS;
    }

    private function uninstall(): void
    {
        collect(
          File::files(Whisky::cwd('.git/hooks'))
        )->filter( fn ($file) => 
          ! str_ends_with($file->getFilename(), 'sample')
        )->each(function ($file): void {
          $path = $file->getPathname();
          $hook = $file->getFilename();
          $contents = File::get($path);
          $command = "eval \"$(./vendor/bin/whisky get-run-cmd {$hook})\"".PHP_EOL;

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
