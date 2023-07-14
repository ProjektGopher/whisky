<?php

use Illuminate\Support\Facades\File;

it('has an install command', function () {
    $this->artisan('list')
        ->expectsOutputToContain('install')
        ->assertExitCode(0);
});

it('fails if git is not initialized', function () {
    File::shouldReceive('exists')
        ->once()
        ->with(base_path('.git'))
        ->andReturnFalse();

    $this->artisan('install')
        ->expectsOutputToContain('Git has not been initialized in this project, aborting...')
        ->assertExitCode(1);
});

it('skips disabled hooks');
it('skips hooks that are already installed');
it('creates an empty script file if not exists');
it('installs hooks');
