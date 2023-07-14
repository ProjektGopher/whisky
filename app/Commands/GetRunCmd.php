<?php

namespace Whisky\Commands;

use Whisky\Whisky;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

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
        if (File::exists(Whisky::base_path('bin/skip-once'))) {
            File::delete(Whisky::base_path('bin/skip-once'));

            return Command::SUCCESS;
        }

        // Check if the hook is disabled in whisky.json
        if (in_array($this->argument('hook'), Whisky::readConfig('disabled'))) {
            return Command::SUCCESS;
        }

        $this->line(Whisky::base_path("bin/run-hook {$this->argument('hook')}"));

        return Command::SUCCESS;
    }
}
