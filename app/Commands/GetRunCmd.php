<?php

namespace ProjektGopher\Whisky\Commands;

use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Whisky;

/**
 * This command was only used to construct the execution path
 * to the `run-hook` bash script, which is now deprecated.
 * Now we can automatically update the hooks snippets.
 */
class GetRunCmd extends Command
{
    protected $signature = 'get-run-cmd {hook}';

    protected $description = 'Get the bash command to run a given hook';

    public function handle(): void
    {
        $bin = Whisky::bin_path();

        $commands = collect([
            "echo 'The snippet in your hook is deprecated. Updating...'",
            "{$bin} update",
            "{$bin} run {$this->argument('hook')}",
        ]);

        $this->line($commands->implode(PHP_EOL));
    }
}
