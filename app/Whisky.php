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
            return getcwd() . DIRECTORY_SEPARATOR . $path;
        }

        return getcwd();
    }

    public static function base_path(string $path = ''): string
    {
        return Phar::running()
            ? Whisky::cwd("vendor/projektgopher/whisky/{$path}")
            : base_path($path);
    }

    public static function readConfig(string $key): string|array|null
    {
        $path = self::cwd('whisky.json');

        $cfg = json_decode(File::get($path), true);

        return data_get($cfg, $key);
    }
}
