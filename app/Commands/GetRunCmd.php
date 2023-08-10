<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Platform;
use ProjektGopher\Whisky\Whisky;

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
        if (File::exists(Platform::cwd('.git/hooks/skip-once'))) {
            File::delete(Platform::cwd('.git/hooks/skip-once'));

            return Command::SUCCESS;
        }

        // Check if the hook is disabled in whisky.json
        if (in_array($this->argument('hook'), Whisky::readConfig('disabled'))) {
            return Command::SUCCESS;
        }

        $bin = Whisky::bin_path();
        $this->line(Whisky::base_path("bin/run-hook {$this->argument('hook')} {$bin}"));

        return Command::SUCCESS;
    }
}
