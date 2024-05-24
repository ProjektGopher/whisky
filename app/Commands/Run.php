<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Hook;
use ProjektGopher\Whisky\Platform;
use ProjektGopher\Whisky\Whisky;
use Symfony\Component\Process\Process as SymfonyProcess;

class Run extends Command
{
    protected $signature = 'run {hook}';

    protected $description = 'Run the scripts for a given hook';

    public function handle(): int
    {
        if (File::missing(Platform::cwd('whisky.json'))) {
            $this->error('Whisky has not been initialized in this project, aborting...');
            $this->line('Run `./vendor/bin/whisky install` to initialize Whisky in this project.');

            return Command::FAILURE;
        }

        if (File::exists(Platform::cwd('.git/hooks/skip-once'))) {
            File::delete(Platform::cwd('.git/hooks/skip-once'));

            return Command::SUCCESS;
        }

        // Check if the hook is disabled in whisky.json
        if (in_array($this->argument('hook'), Whisky::readConfig('disabled'))) {
            return Command::SUCCESS;
        }

        $exitCode = Command::SUCCESS;

        Hook::make($this->argument('hook'))
            ->getScripts()
            ->each(function (string $script) use (&$exitCode): void {
                $isTtySupported = SymfonyProcess::isTtySupported();

                $result = $isTtySupported
                    ? Process::forever()->tty()->run($script)
                    : Process::timeout(300)->run($script);

                if ($result->failed() && ! $isTtySupported) {
                    $this->line($result->errorOutput());
                    $this->line($result->output());
                }

                $exitCode = $exitCode | $result->exitCode();
            });

        return $exitCode;
    }
}
