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
        ->with(Platform::getGitDir('hooks/skip-once'))
        ->andReturnTrue();

    File::shouldReceive('delete')
        ->once()
        ->with(Platform::getGitDir('hooks/skip-once'))
        ->andReturnTrue();

    $tmp_file = Platform::temp_test_path('whisky_test_commit_msg');

    File::shouldReceive('get')
        ->with(Platform::cwd('whisky.json'))
        ->andReturn(<<<"JSON"
        {
            "disabled": [],
            "hooks": {
                "pre-commit": [
                    "echo \"poop\" > {$tmp_file}"
                ]
            }
        }
        JSON);

    $this->artisan('run pre-commit')
        ->assertExitCode(0);

    expect(file_exists($tmp_file))
        ->toBeFalse();
});

it('accepts an optional argument and the argument is correct', function () {
    File::shouldReceive('missing')
        ->with(Platform::cwd('whisky.json'))
        ->andReturnFalse();

    File::shouldReceive('exists')
        ->with(Platform::cwd('.git/hooks/skip-once'))
        ->andReturnFalse();

    $tmp_file = Platform::temp_test_path('whisky_test_commit_msg');

    /**
     * We need to send the output to the disk
     * otherwise the output is sent to the
     * test output and we can't check it.
     */
    File::shouldReceive('get')
        ->with(Platform::cwd('whisky.json'))
        ->andReturn(<<<"JSON"
        {
            "disabled": [],
            "hooks": {
                "commit-msg": [
                    "echo \"$1\" > {$tmp_file}"
                ]
            }
        }
        JSON);

    $this->artisan('run commit-msg ".git/COMMIT_EDITMSG"')
        ->assertExitCode(0);

    expect(file_get_contents($tmp_file))
        ->toContain('.git/COMMIT_EDITMSG');

    unlink($tmp_file);
});

it('still works if no arguments are passed to run command', function () {
    File::shouldReceive('missing')
        ->with(Platform::cwd('whisky.json'))
        ->andReturnFalse();

    File::shouldReceive('exists')
        ->with(Platform::cwd('.git/hooks/skip-once'))
        ->andReturnFalse();

    $tmp_file = Platform::temp_test_path('whisky_test_pre_commit');

    File::shouldReceive('get')
        ->with(Platform::cwd('whisky.json'))
        ->andReturn(<<<"JSON"
        {
            "disabled": [],
            "hooks": {
                "pre-commit": [
                    "echo \"pre-commit\" > {$tmp_file}"
                ]
            }
        }
        JSON);

    $this->artisan('run pre-commit')
        ->assertExitCode(0);

    unlink($tmp_file);
});

it('handles a missing expected argument gracefully', function () {
    File::shouldReceive('missing')
        ->with(Platform::cwd('whisky.json'))
        ->andReturnFalse();

    File::shouldReceive('exists')
        ->with(Platform::cwd('.git/hooks/skip-once'))
        ->andReturnFalse();

    $tmp_file = Platform::temp_test_path('whisky_test_commit_msg');

    File::shouldReceive('get')
        ->with(Platform::cwd('whisky.json'))
        ->andReturn(<<<"JSON"
        {
            "disabled": [],
            "hooks": {
                "commit-msg": [
                    "echo \"$1\" > {$tmp_file}"
                ]
            }
        }
        JSON);

    $this->artisan('run commit-msg')
        ->assertExitCode(0);

    expect(file_exists($tmp_file))
        ->toBeTrue();

    unlink($tmp_file);
});
