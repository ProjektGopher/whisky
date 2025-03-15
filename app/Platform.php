<?php

namespace ProjektGopher\Whisky;

use Illuminate\Support\Facades\File;

class Platform
{
    public static function cwd(string $path = ''): string
    {
        if ($path) {
            return static::normalizePath(getcwd().'/'.$path);
        }

        return static::normalizePath(getcwd());
    }

    public static function temp_test_path(string $path = ''): string
    {
        if ($path) {
            return static::cwd("tests/tmp/{$path}");
        }

        return static::cwd('tests/tmp');
    }

    public static function normalizePath(string $path): string
    {
        $path = str_replace(' ', '\ ', $path);

        if ((new self)->isWindows()) {
            return str_replace('\\', '/', $path);
        }

        return $path;
    }

    public static function getGlobalComposerHome(): string
    {
        return rtrim(shell_exec('composer -n global config home --quiet'), "\n");
    }

    public static function getGlobalComposerBinDir(): string
    {
        return rtrim(shell_exec('composer -n global config bin-dir --absolute --quiet'), "\n");
    }

    public function determineQuote(): string
    {
        return $this->isWindows() ? '"' : "'";
    }

    public function isWindows(): bool
    {
        return str_starts_with(strtoupper(PHP_OS), 'WIN');
    }

    public function isNotWindows(): bool
    {
        return ! $this->isWindows();
    }

    public function gitIsInitialized(): bool
    {
        return File::exists(Platform::cwd('.git'));
    }

    public function gitIsNotInitialized(): bool
    {
        return ! $this->gitIsInitialized();
    }
}
