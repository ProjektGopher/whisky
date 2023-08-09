<?php

namespace ProjektGopher\Whisky;

use Phar;

class Whisky
{
    public function __construct()
    {
        //
    }

    public static function cwd(string $path = ''): string
    {
        if ($path) {
            return Whisky::normalizePath(getcwd().'/'.$path);
        }

        return Whisky::normalizePath(getcwd());
    }

    public static function bin(): string
    {
        return Whisky::normalizePath(match (true) {
            self::dogfooding() => self::cwd('whisky'),
            self::isRunningGlobally() => '/usr/local/bin/whisky', // TODO
            default => self::cwd('vendor/bin/whisky'),
        });
    }

    public static function dogfooding(): bool
    {
        return self::cwd() === self::base_path();
    }

    // TODO
    public static function isRunningGlobally(): bool
    {
        return false;
    }

    public static function base_path(string $path = ''): string
    {
        return Whisky::normalizePath(Phar::running()
            ? Whisky::cwd("vendor/projektgopher/whisky/{$path}")
            : base_path($path));
    }

    public static function readConfig(string $key): string|array|null
    {
        $cfg = FileJson::make(self::cwd('whisky.json'))->read();

        return data_get($cfg, $key);
    }

    public static function normalizePath(string $path): string
    {
        if (Whisky::isWindows()) {
            return str_replace('\\', '/', $path);
        }

        return $path;
    }

    public static function isWindows(): bool
    {
        return str_starts_with(strtoupper(PHP_OS), 'WIN');
    }
}
