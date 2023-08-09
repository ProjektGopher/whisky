<?php

namespace ProjektGopher\Whisky;

use Phar;

class Whisky
{
    public function __construct()
    {
        //
    }

    public static function bin(): string
    {
        return Platform::normalizePath(match (true) {
            self::dogfooding() => Platform::cwd('whisky'),
            self::isRunningGlobally() => '/usr/local/bin/whisky', // TODO
            default => Platform::cwd('vendor/bin/whisky'),
        });
    }

    public static function dogfooding(): bool
    {
        return Platform::cwd() === self::base_path();
    }

    // TODO
    public static function isRunningGlobally(): bool
    {
        // composer -n config --global home
        //
        return false;
    }

    // TODO
    public static function isInstalledGlobally(): bool
    {
        // composer -n config --global home
        // composer -n global config bin-dir --absolute --quiet
        return false;
    }

    public static function base_path(string $path = ''): string
    {
        return Platform::normalizePath(Phar::running()
            ? Platform::cwd("vendor/projektgopher/whisky/{$path}")
            : base_path($path));
    }

    public static function readConfig(string $key): string|array|null
    {
        $cfg = FileJson::make(Platform::cwd('whisky.json'))->read();

        return data_get($cfg, $key);
    }
}
