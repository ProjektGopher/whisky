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

    public static function normalizePath(string $path): string
    {
        if ((new self)->isWindows()) {
            return str_replace('\\', '/', $path);
        }

        return $path;
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
