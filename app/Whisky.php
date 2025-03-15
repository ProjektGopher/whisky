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
            self::isRunningGlobally() => Platform::getGlobalComposerHome().'/'.$code_path,
            default => Platform::cwd($code_path),
        });
    }

    public static function bin_path(): string
    {
        $path = Platform::normalizePath(match (true) {
            self::dogfooding() => Platform::cwd('whisky'),
            self::isRunningGlobally() => Platform::getGlobalComposerBinDir().'/whisky',
            default => Platform::cwd('vendor/bin/whisky'),
        });

        return Platform::escapeSpaces($path);
    }

    public static function dogfooding(): bool
    {
        return Platform::cwd() === Platform::normalizePath(base_path());
    }

    public static function isInstalledGlobally(): bool
    {
        return File::exists(Platform::getGlobalComposerBinDir().'/whisky');
    }

    public static function isRunningGlobally(): bool
    {
        return str_starts_with(base_path(), 'phar://'.Platform::getGlobalComposerHome());
    }

    public static function isInstalledLocally(): bool
    {
        return File::exists(Platform::cwd('vendor/projektgopher/whisky'));
    }

    public static function isRunningLocally(): bool
    {
        return str_starts_with(base_path(), 'phar://'.Platform::cwd('vendor/bin'));
    }

    public static function readConfig(string $key): string|array|null
    {
        $cfg = FileJson::make(Platform::cwd('whisky.json'))->read();

        return data_get($cfg, $key);
    }
}
