<?php

namespace App\Commands;

use App\Whisky;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class Scripts extends Command
{
    protected $signature = 'scripts {hook}';

    protected $description = 'Get a list of scripts for a given hook';

    public function handle(): int
    {
        if (File::missing(Whisky::cwd('whisky.json'))) {
            $this->error('Whisky has not been initialized in this project, aborting...');
            $this->line('Run `./vendor/bin/whisky install` to initialize Whisky in this project.');

            return Command::FAILURE;
        }

        $this->getScripts($this->argument('hook'))->each(function ($script): void {
            if (str_starts_with($script, '@')) {
                $this->line(substr($script, 1));

                return;
            }
            $this->line($this->buildScriptPath($script));
        });

        return Command::SUCCESS;
    }

    private function getScripts(string $hook): Collection
    {
        return collect(Whisky::readConfig("hooks.{$hook}"));
    }

    private function buildScriptPath(string $script): string
    {
        return implode([Whisky::readConfig('scriptsDir'), DIRECTORY_SEPARATOR, $script]);
    }
}
