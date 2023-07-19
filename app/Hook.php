<?php

namespace ProjektGopher\Whisky;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Hook
{
    public function __construct(
        public string $hook,
    ) {
    }

    public static function make(string $hook): Hook
    {
        return new Hook($hook);
    }

    public function install(): void
    {
        throw new Exception('Not implemented');
    }

    public function uninstall(): void
    {
        throw new Exception('Not implemented');
    }

    public function validate(): bool
    {
        throw new Exception('Not implemented');
    }

    // use ensureFileExists instead?
    public function fileExists(): bool
    {
        return File::exists(Whisky::cwd(".git/hooks/{$this->hook}"));
    }

    public function isInstalled(): bool
    {
        throw new Exception('Not implemented');
        // return collect(
        //     File::files(Whisky::cwd('.git/hooks'))
        // )->filter( fn ($file) =>
        //     ! str_ends_with($file->getFilename(), 'sample')
        // )->contains(function ($file): bool {
        //     $path = $file->getPathname();
        //     $hook = $file->getFilename();
        //     $contents = File::get($path);
        //     $command = "eval \"$(./vendor/bin/whisky get-run-cmd {$hook})\"".PHP_EOL;

        //     return str_contains($contents, $command);
        // });
    }

    public function isDisabled(): bool
    {
        return in_array($this->hook, Whisky::readConfig('hooks.disabled'));
    }

    public function getScripts(): Collection
    {
        return collect(Whisky::readConfig("hooks.{$this->hook}"));
    }
}
