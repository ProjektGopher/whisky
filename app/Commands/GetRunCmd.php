<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\File;

/**
 * This command is basically only needed to build the execution path
 * to the `run` bash script, or to skip the hooks once.
 */
class GetRunCmd extends Command
{
    protected $signature = 'get-run-cmd {hook}';

    protected $description = 'Get the bash command to run a given hook';

    public function handle(): int
    {
        if (File::exists(__DIR__.'/../../bin/skip-once')) {
            File::delete(__DIR__.'/../../bin/skip-once');

            return Command::SUCCESS;
        }

        $this->line(__DIR__."/../../bin/run-hook {$this->argument('hook')}");

        return Command::SUCCESS;
    }
}
