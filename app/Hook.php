<?php

namespace ProjektGopher\Whisky;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Hook
{
    public string $name;

    public string $bin;

    public function __construct(
        public string $hook,
    ) {
        $this->name = $hook;
        $this->bin = Whisky::bin_path();
    }

    public function uninstall(): bool
    {
        $path = Platform::cwd(".git/hooks/{$this->hook}");

        if ($this->fileIsMissing()) {
            // This should be unreachable.
            throw new FileNotFoundException("Could not find {$path}");
        }

        $contents = File::get($path);
        $commands = [
            "eval \"$({$this->bin} get-run-cmd {$this->hook})\"".PHP_EOL,
            // TODO: legacy - handle upgrade somehow
            "eval \"$(./vendor/bin/whisky get-run-cmd {$this->hook})\"".PHP_EOL,
        ];

        if (! Str::contains($contents, $commands)) {
            return false;
        }

        File::put($path, str_replace($commands, '', $contents));

        return true;
    }

    // use ensureFileExists instead?
    public function fileExists(): bool
    {
        return File::exists(Platform::cwd(".git/hooks/{$this->hook}"));
    }

    /**
     * Checks if the hook is enabled **in git**.
     */
    public function isEnabled(): bool
    {
        return $this->fileExists();
    }

    /**
     * Checks if the hook is not enabled **in git**.
     */
    public function isNotEnabled(): bool
    {
        return ! $this->isEnabled();
    }

    /**
     * Creates the git hook file.
     */
    public function enable(): void
    {
        File::put(Platform::cwd(".git/hooks/{$this->hook}"), '#!/bin/sh'.PHP_EOL);
    }

    /**
     * Checks if the hook uses Whisky.
     */
    public function isInstalled(): bool
    {
        return Str::contains(
            File::get(Platform::cwd(".git/hooks/{$this->hook}")),
            [
                "eval \"$({$this->bin} get-run-cmd {$this->hook})\"",
                // TODO: legacy - handle upgrade somehow
                "eval \"$(./vendor/bin/whisky get-run-cmd {$this->hook})\"",
            ],
        );
    }

    /**
     * Adds snippet for calling Whisky to the git hook file.
     */
    public function install(): void
    {
        File::append(
            Platform::cwd(".git/hooks/{$this->hook}"),
            "eval \"$({$this->bin} get-run-cmd {$this->hook})\"".PHP_EOL,
        );
    }

    /**
     * Checks if the hook is disabled in `whisky.json`.
     */
    public function isDisabled(): bool
    {
        return in_array($this->hook, Whisky::readConfig('hooks.disabled'));
    }

    public function getScripts(): Collection
    {
        return collect(Whisky::readConfig("hooks.{$this->hook}"));
    }

    ////////////////////////////////////////
    ////         Static methods         ////
    ////////////////////////////////////////

    /**
     * Static Constructor.
     */
    public static function make(string $hook): Hook
    {
        return new Hook($hook);
    }

    /**
     * Get a collection of all hooks listed in `whisky.json`.
     *
     * TODO: this should accept an option to build by files
     * in the `.git/hooks` dir instead of `whisky.json`.
     * possibly `FROM_GIT`|`FROM_CONFIG`|`ALL` consts.
     */
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
}
