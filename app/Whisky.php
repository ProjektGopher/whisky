<?php

namespace ProjektGopher\Whisky;

use Illuminate\Support\Facades\File;
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
            return getcwd().DIRECTORY_SEPARATOR.$path;
        }

        return getcwd();
    }

    public static function bin(): string
    {
        return match (true) {
            self::dogfooding() => self::cwd('whisky'),
            self::isRunningGlobally() => '/usr/local/bin/whisky', // TODO
            default => self::cwd('vendor/bin/whisky'),
        };
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
        return Phar::running()
            ? Whisky::cwd("vendor/projektgopher/whisky/{$path}")
            : base_path($path);
    }

    public static function readConfig(string $key): string|array|null
    {
        $cfg = File::json(static::cwd('whisky.json'));

        return data_get($cfg, $key);
    }
}
