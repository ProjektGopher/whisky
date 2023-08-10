<?php

use Illuminate\Support\Collection;
use ProjektGopher\Whisky\Hook;

describe('all method', function () {
    it('returns all hooks', function () {
        $hooks = Hook::all(Hook::FROM_GIT | Hook::FROM_CONFIG);

        expect($hooks)->toBeInstanceOf(Collection::class);
        expect($hooks->count())->toBeGreaterThan(0);
    });
});
