<?php

use Illuminate\Support\Facades\File;
use ProjektGopher\Whisky\Platform;

it('deletes skip-once file if exists and outputs nothing', function () {
    File::shouldReceive('exists')
        ->once()
        ->with(Platform::cwd('.git/hooks/skip-once'))
        ->andReturnTrue();

    File::shouldReceive('delete')
        ->once()
        ->with(Platform::cwd('.git/hooks/skip-once'))
        ->andReturnTrue();

    $this->artisan('run pre-commit')
        ->doesntExpectOutputToContain('run-hook')
        ->assertExitCode(0);
})->skip();
