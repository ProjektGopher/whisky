<?php

namespace ProjektGopher\Whisky;

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
}
