<?php

namespace Whisky\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use Whisky\Whisky;

class Uninstall extends Command
{
    protected $signature = 'uninstall {--json}';

    protected $description = 'Uninstall git hooks';

    public function handle(): int
    {
        collect(
            File::files(Whisky::cwd('.git/hooks'))
        )->filter(
            fn ($file) => ! str_ends_with($file->getFilename(), 'sample')
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

        if (
            $this->option('json') ||
            $this->confirm('Would you also like to remove whisky.json?')
        ) {
            File::delete(Whisky::cwd('whisky.json'));
            $this->info('whisky.json removed.');
        }

        return Command::SUCCESS;
    }
}
