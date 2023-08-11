<?php

namespace ProjektGopher\Whisky;

use Illuminate\Support\Facades\File;

class Whisky
{
    public static function base_path(string $path = ''): string
    {
        $code_path = "vendor/projektgopher/whisky/{$path}";

        return Platform::normalizePath(match (true) {
            self::dogfooding() => base_path($path),
            self::isRunningLocally() => Platform::cwd($code_path),
            default => Platform::getGlobalComposerHome().'/'.$code_path,
        });
    }

    public static function bin_path(): string
    {
        return Platform::normalizePath(match (true) {
            self::dogfooding() => Platform::cwd('whisky'),
            self::isRunningLocally() => Platform::cwd('vendor/bin/whisky'),
            default => Platform::getGlobalComposerBinDir().'/whisky',
        });
    }

    public static function dogfooding(): bool
    {
        return Platform::cwd() === Platform::normalizePath(base_path());
    }

    public static function isInstalledGlobally(): bool
    {
        return File::exists(Platform::getGlobalComposerBinDir().'/whisky');
    }

    public static function isInstalledLocally(): bool
    {
        return File::exists(Platform::cwd('vendor/bin/whisky'));
    }

    public static function isRunningGlobally(): bool
    {
        // TODO: appears broken on WAMP - base_path() and getGlobalComposerHome() differ
        // return str_starts_with(base_path(), 'phar://'.Platform::getGlobalComposerHome());
        return ! self::isRunningLocally() && ! self::dogfooding();
    }

    public static function isRunningLocally(): bool
    {
        return str_starts_with(base_path(), 'phar://'.Platform::cwd());
    }

    public static function readConfig(string $key): string|array|null
    {
        $cfg = FileJson::make(Platform::cwd('whisky.json'))->read();

        return data_get($cfg, $key);
    }
}
