<?php

namespace ProjektGopher\Whisky;

use Illuminate\Support\Facades\File;

class Platform
{
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
        return File::exists(Whisky::cwd('.git'));
    }

    public function gitIsNotInitialized(): bool
    {
        return ! $this->gitIsInitialized();
    }
}
