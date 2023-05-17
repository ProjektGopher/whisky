<?php

use Illuminate\Support\Facades\File;

it('has a skip once command', function () {
  $this->artisan('list')
      ->expectsOutputToContain('git-hooks:skip-once')
      ->assertExitCode(0);
});

it('creates a skip-once file', function () {
  $this->artisan('git-hooks:skip-once')
      ->expectsOutputToContain('Next hook will be skipped.')
      ->assertExitCode(0);

  expect(File::exists(__DIR__.'/../../bin/skip-once'))->toBeTrue();

  // Cleanup
  File::delete(__DIR__.'/../../bin/skip-once');
});
