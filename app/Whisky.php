<?php

namespace App;

use Phar;
use Illuminate\Support\Facades\File;

class Whisky
{
    public function __construct()
    {
        //
    }

    public static function cwd(string $path = ''): string
    {
        if ($path) {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
            return getcwd() . DIRECTORY_SEPARATOR . $path;
        }

        return getcwd();
    }

    public static function base_path(string $path = ''): string
    {
        if (! Phar::running()) {
            return base_path($path);
        }

        $path = File::exists(Whisky::cwd("vendor/bin/whisky"))
            ? Whisky::cwd("vendor/projektgopher/whisky/{$path}")
            : self::getGlobalComposerHome().'/vendor/projektgopher/whisky/'.$path;

        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    public static function readConfig(string $key): string|array
    {
        $path = self::cwd('whisky.json');

        $cfg = json_decode(File::get($path), true);

        return data_get($cfg, $key);
    }

    public static function getGlobalComposerHome(): string
    {
        return rtrim(shell_exec('composer -n config --global home'), "\n");
    }
}
