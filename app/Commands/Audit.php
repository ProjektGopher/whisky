<?php

namespace ProjektGopher\Whisky\Commands;

use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;
use ProjektGopher\Whisky\Platform;
use ProjektGopher\Whisky\Whisky;

class Audit extends Command
{
    protected $signature = 'audit';

    protected $description = 'Print table with information for diagnostic purposes.';

    public function handle(): int
    {
        $platform = new Platform();

        $this->table(
            ['key', 'value'],
            [
                ['- Whisky -', ''],
                ['installed globally?', $this->isWhiskyInstalledGlobally()],
                ['running globally?', Whisky::isRunningGlobally() ? 'yes' : 'no'],
                ['installed locally?', $this->isWhiskyInstalledLocally()],
                ['running locally?', Whisky::isRunningLocally() ? 'yes' : 'no'],
                ['dogfooding?', Whisky::dogfooding() ? 'yes' : 'no'],
                ['base path', Whisky::base_path()],
                ['bin path', Whisky::bin_path()],
                // ['readConfig?', Whisky::readConfig()],
                ['- Platform -', ''],
                ['cwd', Platform::cwd()],
                ['getGlobalComposerHome', Platform::getGlobalComposerHome()],
                ['getGlobalComposerBinDir', Platform::getGlobalComposerBinDir()],
                ['isWindows', $platform->isWindows() ? 'yes' : 'no'],
                ['isNotWindows', $platform->isNotWindows() ? 'yes' : 'no'],
                ['gitIsInitialized', $platform->gitIsInitialized() ? 'yes' : 'no'],
                ['gitIsNotInitialized', $platform->gitIsNotInitialized() ? 'yes' : 'no'],
                ['- global -', ''],
                ['base_path', base_path()],
                ['normalized base_path', Platform::normalizePath(base_path())],
            ],
        );

        return Command::SUCCESS;
    }

    protected function isWhiskyInstalledGlobally(): string
    {
        if (! Whisky::isInstalledGlobally()) {
            return 'no';
        }

        $result = Process::run('composer global show projektgopher/whisky --format=json');
        $version = json_decode($result->output(), true)['versions'][0];

        return "yes ({$version})";
    }

    protected function isWhiskyInstalledLocally(): string
    {
        if (! Whisky::isInstalledLocally()) {
            return 'no';
        }

        $result = Process::run('composer show projektgopher/whisky --format=json');
        $version = json_decode($result->output(), true)['versions'][0];

        return "yes ({$version})";
    }
}
