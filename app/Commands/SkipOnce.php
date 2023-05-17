<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\File;

class SkipOnce extends Command
{
    protected $signature = 'skip-once';

    protected $description = 'Skip only the next hook';

    public function handle(): int
    {
        File::put(__DIR__.'/../../bin/skip-once', '');

        $this->info('Next hook will be skipped.');
        $this->line('If the action you\'re about to take has a `pre` and `post` hook');
        $this->line('(like commit, or push), only the `pre` hook will be skipped.');
        $this->info('To skip all hooks, run `git config hooks.skip true`');

        return Command::SUCCESS;
    }
}
