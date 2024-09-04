<?php

use Illuminate\Process\FakeProcessResult;
use Illuminate\Support\Facades\Process;
use ProjektGopher\Whisky\Platform;

it('has an install command', function () {
    $this->artisan('list')
        ->expectsOutputToContain('install')
        ->assertExitCode(0);
});

it('fails if git is not initialized', function () {
    Process::shouldReceive('run')
        ->once()
        ->with(Platform::GIT_DIR_CMD)
        ->andReturn(new FakeProcessResult(
            command: Platform::GIT_DIR_CMD,
            exitCode: 1,
            output: '',
            errorOutput: 'fatal: not a git repository (or any of the parent directories): .git\n',
        ));

    $this->artisan('install')
        ->expectsOutputToContain('Git has not been initialized in this project, aborting...')
        ->assertExitCode(1);
});

it('skips disabled hooks');
it('skips hooks that are already installed');
it('creates an empty script file if not exists');
it('installs hooks');
