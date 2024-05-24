<?php

use Illuminate\Support\Facades\File;
use ProjektGopher\Whisky\Platform;

it('deletes skip-once file if exists as long as whisky.json exists', function () {
    File::shouldReceive('missing')
        ->once()
        ->with(Platform::cwd('whisky.json'))
        ->andReturnFalse();

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
})->skip('Needs to be refactored so that the hooks don\'t actually run');
