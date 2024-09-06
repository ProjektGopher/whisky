<?php

use Illuminate\Support\Facades\File;
use ProjektGopher\Whisky\Platform;

it('deletes skip-once file if exists as long as whisky.json exists and does not run the hook', function () {
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

    File::shouldReceive('get')
        ->with(Platform::cwd('whisky.json'))
        ->andReturn(<<<'JSON'
        {
            "disabled": [],
            "hooks": {
                "pre-commit": [
                    "echo \"poop\" > /tmp/whisky_test_commit_msg"
                ]
            }
        }
        JSON);

    $this->artisan('run pre-commit')
        ->assertExitCode(0);

    expect(file_exists('/tmp/whisky_test_commit_msg'))
        ->toBeFalse();
});

it('accepts an optional argument and the argument is correct', function () {
    File::shouldReceive('missing')
        ->with(Platform::cwd('whisky.json'))
        ->andReturnFalse();

    File::shouldReceive('exists')
        ->with(Platform::cwd('.git/hooks/skip-once'))
        ->andReturnFalse();

    /**
     * We need to send the output to the disk
     * otherwise the output is sent to the
     * test output and we can't check it.
     */
    File::shouldReceive('get')
        ->with(Platform::cwd('whisky.json'))
        ->andReturn(<<<'JSON'
        {
            "disabled": [],
            "hooks": {
                "commit-msg": [
                    "echo \"$1\" > /tmp/whisky_test_commit_msg"
                ]
            }
        }
        JSON);

    $this->artisan('run commit-msg ".git/COMMIT_EDITMSG"')
        ->assertExitCode(0);

    expect(file_get_contents('/tmp/whisky_test_commit_msg'))
        ->toContain('.git/COMMIT_EDITMSG');

    unlink('/tmp/whisky_test_commit_msg');
});

it('still works if no arguments are passed to run command', function () {
    File::shouldReceive('missing')
        ->with(Platform::cwd('whisky.json'))
        ->andReturnFalse();

    File::shouldReceive('exists')
        ->with(Platform::cwd('.git/hooks/skip-once'))
        ->andReturnFalse();

    File::shouldReceive('get')
        ->with(Platform::cwd('whisky.json'))
        ->andReturn(<<<'JSON'
        {
            "disabled": [],
            "hooks": {
                "pre-commit": [
                    "echo \"pre-commit\" > /dev/null"
                ]
            }
        }
        JSON);

    $this->artisan('run pre-commit')
        ->assertExitCode(0);
});

it('handles a missing expected argument gracefully', function () {
    File::shouldReceive('missing')
        ->with(Platform::cwd('whisky.json'))
        ->andReturnFalse();

    File::shouldReceive('exists')
        ->with(Platform::cwd('.git/hooks/skip-once'))
        ->andReturnFalse();

    File::shouldReceive('get')
        ->with(Platform::cwd('whisky.json'))
        ->andReturn(<<<'JSON'
        {
            "disabled": [],
            "hooks": {
                "commit-msg": [
                    "echo \"$1\" > /tmp/whisky_test_commit_msg"
                ]
            }
        }
        JSON);

    $this->artisan('run commit-msg')
        ->assertExitCode(0);

    expect(file_get_contents('/tmp/whisky_test_commit_msg'))
        ->toBe("\n");

    unlink('/tmp/whisky_test_commit_msg');
});
