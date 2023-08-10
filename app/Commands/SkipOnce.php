<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Platform;

class SkipOnce extends Command
{
    protected $signature = 'skip-once';

    protected $description = 'Skip only the next hook';

    public function handle(): int
    {
        File::put(Platform::cwd('.git/hooks/skip-once'), '');

        $this->info('Next hook will be skipped.');
        $this->line('If the action you\'re about to take has a `pre` and `post` hook');
        $this->line('(like commit, or push), only the `pre` hook will be skipped.');
        $this->info('To skip all hooks, run `git config hooks.skip true`');

        return Command::SUCCESS;
    }
}
