<?php

namespace ProjektGopher\Whisky;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Hook
{
    public string $name;

    public function __construct(
        public string $hook,
    ) {
        $this->name = $hook;
    }

    public static function make(string $hook): Hook
    {
        return new Hook($hook);
    }

    public static function all(): Collection
    {
        return collect(array_keys(Whisky::readConfig('hooks')))->map(
            fn (string $hook) => new Hook($hook),
        );
    }

    public static function each(callable $callable): Collection
    {
        return self::all()->each($callable);
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
        return File::exists(Platform::cwd(".git/hooks/{$this->hook}"));
    }

    /**
     * Checks if the hook is enabled in git.
     */
    public function isEnabled(): bool
    {
        return $this->fileExists();
    }

    public function isNotEnabled(): bool
    {
        return ! $this->isEnabled();
    }

    public function enable(): void
    {
        File::put(Platform::cwd(".git/hooks/{$this->hook}"), '#!/bin/sh'.PHP_EOL);
    }

    /**
     * Checks if the hook uses Whisky.
     */
    public function isInstalled(): bool
    {
        $bin = Whisky::bin();

        return Str::contains(
            File::get(Platform::cwd(".git/hooks/{$this->hook}")),
            [
                "eval \"$({$bin} get-run-cmd {$this->hook})\"",
                // TODO: legacy - handle upgrade somehow
                "eval \"$(./vendor/bin/whisky get-run-cmd {$this->hook})\"",
            ],
        );
    }

    public function install(): void
    {
        $bin = Whisky::bin();
        File::append(
            Platform::cwd(".git/hooks/{$this->hook}"),
            "eval \"$({$bin} get-run-cmd {$this->hook})\"".PHP_EOL,
        );
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
