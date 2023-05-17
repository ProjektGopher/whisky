<?php

use Illuminate\Support\Facades\File;

it('deletes skip-once file if exists and outputs nothing', function () {
    File::shouldReceive('exists')
        ->once()
        ->with(__DIR__.'/../../bin/skip-once')
        ->andReturnTrue();

    File::shouldReceive('delete')
        ->once()
        ->with(__DIR__.'/../../bin/skip-once')
        ->andReturnTrue();

    $this->artisan('git-hooks:get-run-cmd pre-commit')
        ->doesntExpectOutputToContain('run-hook')
        ->assertExitCode(0);
});

it('points correctly to the run-hook script');
