<?php

namespace App;

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

    public static function readConfig(string $key): string|array
    {
        $path = self::cwd('whisky.json');

        $cfg = json_decode(File::get($path), true);

        return data_get($cfg, $key);
    }
}
