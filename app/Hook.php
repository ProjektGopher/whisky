<?php

namespace ProjektGopher\Whisky;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SplFileInfo;

class Hook
{
    const FROM_CONFIG = 1;

    const FROM_GIT = 2;

    const ALL = 3;

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
        $commands = $this->getSnippets()
            ->map(fn (string $snippet): string => $snippet.PHP_EOL)
            ->toArray();

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

    public function fileIsMissing(): bool
    {
        return ! $this->fileExists();
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
            $this->getSnippets()->toArray(),
        );
    }

    /**
     * Adds snippet for calling Whisky to the git hook file.
     */
    public function install(): void
    {
        File::append(
            Platform::cwd(".git/hooks/{$this->hook}"),
            $this->getSnippets()->first().PHP_EOL,
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

    /**
     * Collect the bash snippet history for calling the Whisky bin.
     * The current version of the snippet should always be first.
     * We keep this history to make updating our hooks easier.
     *
     * @return Collection<int, string>
     */
    public function getSnippets(): Collection
    {
        return collect([
            "{$this->bin} run {$this->hook}",
            // Legacy Snippets.
            "eval \"$({$this->bin} get-run-cmd {$this->hook})\"",
            "eval \"$(./vendor/bin/whisky get-run-cmd {$this->hook})\"",
        ]);
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
     * Get a collection of hooks.
     *
     * Passing `Hook::FROM_CONFIG` flag will return hooks that are listed in `whisky.json` config.
     * Passing `Hook::FROM_GIT`    flag will return hooks that are enabled in `.git/hooks` folder.
     * Passing `Hook::ALL`, or `Hook::FROM_CONFIG | Hook::FROM_GIT` to flag will return all hooks.
     */
    public static function all(int $flags = self::FROM_CONFIG): Collection
    {
        $result = collect();

        if ($flags & self::FROM_GIT) {
            $result->push(...collect(File::files(Platform::cwd('.git/hooks')))
                ->map(fn (SplFileInfo $file) => $file->getFilename())
                ->filter(fn (string $filename) => ! str_ends_with($filename, 'sample'))
            );
        }

        if ($flags & self::FROM_CONFIG) {
            $result->push(...array_keys(Whisky::readConfig('hooks')));
        }

        // dd($result->unique());

        return $result->unique()->map(
            fn (string $hook) => new Hook($hook),
        );
    }

    public static function each(callable $callable, int $flags = self::FROM_CONFIG): Collection
    {
        return self::all($flags)->each($callable);
    }
}
