<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Whisky;
use SplFileInfo;

class Uninstall extends Command
{
    protected $signature = 'uninstall {--json}';

    protected $description = 'Uninstall git hooks';

    public function handle(): int
    {
        collect(
            File::files(Whisky::cwd('.git/hooks'))
        )->filter(
            fn (SplFileInfo $file) => ! str_ends_with($file->getFilename(), 'sample')
        )->each(function (SplFileInfo $file): void {
            $bin = Whisky::bin();
            $path = $file->getPathname();
            $hook = $file->getFilename();
            $contents = File::get($path);
            $commands = [
                "eval \"$({$bin} get-run-cmd {$hook})\"".PHP_EOL,
                // TODO: legacy - handle upgrade somehow
                "eval \"$(./vendor/bin/whisky get-run-cmd {$hook})\"".PHP_EOL,
            ];

            if (! Str::contains($contents, $commands)) {
                return;
            }

            $contents = str_replace(
                $commands,
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
