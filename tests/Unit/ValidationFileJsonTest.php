<?php

use Illuminate\Support\Facades\File;
use ProjektGopher\Whisky\FileJson;

it('fails if json is invalid', function ($responseFileContent) {

    $pathFile = '/tmp/whisky.json';

    File::shouldReceive('get')
        ->byDefault()
        ->with($pathFile)
        ->andReturn($responseFileContent);

    $this->expectException(\Exception::class);
    // $this->expectExceptionMessage('Invalid JSON: in '.$pathFile);
    $this->expectExceptionMessageMatches('/Invalid JSON: .+ in '.str_replace('/', '\/', $pathFile).'/');

    FileJson::make($pathFile)->read();

})->with([
    // no json => Syntax error
    'foo',
    // missing quotes => Syntax error
    '{ foo: "var" }',
    // wrong operator => Syntax error
    '{ "foo" = "var" }',
    // wrong separator => Syntax error
    '{ "foo", "var" }',
    // trailing comma => Syntax error
    '{ "foo": "var", "var": "foo", }',
    // missing closing quote => Control character error, possibly incorrectly encoded
    '{ "foo": "var }',
]);

it('fails if json is valid but not valid with schema because is not object', function ($responseFileContent) {

    $pathFile = '/tmp/whisky.json';

    File::shouldReceive('get')
        ->byDefault()
        ->with($pathFile)
        ->andReturnUsing(fn () => $responseFileContent);

    $this->expectException(\Exception::class);
    // $this->expectExceptionMessage('Invalid JSON schema: in '.$pathFile);
    $this->expectExceptionMessageMatches('/Invalid JSON schema: .+ in '.str_replace('/', '\/', $pathFile).'/');

    $config = FileJson::make($pathFile)->read();

})->with([
    // bool
    'true',
    // bool
    'false',
    // null
    'null',
    // string
    '"foo"',
    // int
    '1',
    // float
    '1.1',
    // array
    '[]',
]);

it('fails if json is valid but not valid with schema', function ($responseFileContent) {

    $pathFile = '/tmp/whisky.json';

    File::shouldReceive('get')
        ->byDefault()
        ->with($pathFile)
        ->andReturn($responseFileContent);

    $this->expectException(\Exception::class);
    // $this->expectExceptionMessage('Invalid JSON schema: in '.$pathFile);
    $this->expectExceptionMessageMatches('/Invalid JSON schema: .+ in '.str_replace('/', '\/', $pathFile).'/');

    $config = FileJson::make($pathFile)->read();

})->with([
    // empty object
    '{}',
    // missing "hooks"
    '{ "foo": "bar" }',
    // "disabled" is not array (bool)
    '{ "disabled": true, "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "disabled" is not array (bool)
    '{ "disabled": false, "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }', // "disabled" is not array
    // "disabled" is not array (null)
    '{ "disabled": null, "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }', // "disabled" is not array
    // "disabled" is not array (string)
    '{ "disabled": "string", "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }', // "disabled" is not array
    // "disabled" is not array (int)
    '{ "disabled": 1, "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }', // "disabled" is not array
    // "disabled" is not array (float)
    '{ "disabled": 1.1, "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }', // "disabled" is not array
    // "disabled" is not array (object)
    '{ "disabled": {}, "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }', // "disabled" is not array
    // "hooks.[hook]" is not array (bool)
    '{ "disabled": [], "hooks": { "pre-commit": true, "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is not array (bool)
    '{ "disabled": [], "hooks": { "pre-commit": false, "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is not array (null)
    '{ "disabled": [], "hooks": { "pre-commit": null, "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is not array (string)
    '{ "disabled": [], "hooks": { "pre-commit": "string", "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is not array (int)
    '{ "disabled": [], "hooks": { "pre-commit": 1, "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is not array (float)
    '{ "disabled": [], "hooks": { "pre-commit": 1.1, "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is not array (object)
    '{ "disabled": [], "hooks": { "pre-commit": {}, "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is empty array
    '{ "disabled": [], "hooks": { "pre-commit": [], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is empty string
    '{ "disabled": [], "hooks": { "pre-commit": [ "" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "hooks.[hook]" is only spaces string
    '{ "disabled": [], "hooks": { "pre-commit": [ " " ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "foo" (or new properties) is not allowed
    '{ "foo": "bar", "disabled": [], "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "post-push" is not git hook, only this values are allowed in array "disabled"
    '{ "disabled": [ "post-push" ], "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
    // "post-push" is not git hook, only this values are allowed as propery in in object "hooks" (no new properties)
    '{ "disabled": [], "hooks": { "post-push": [ "echo post-push" ], "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
]);

it('valid json', function ($responseFileContent) {

    $pathFile = '/tmp/whisky.json';

    File::shouldReceive('get')
        ->byDefault()
        ->with($pathFile)
        ->andReturn($responseFileContent);

    $config = FileJson::make($pathFile)->read();
    expect($config)->toBeArray();
    $disabledProperty = data_get($config, 'disabled');
    expect($disabledProperty)->toBeArray();
    expect($disabledProperty)->toBe([]);
    $hooksProperty = data_get($config, 'hooks');
    expect($hooksProperty)->toBeArray();
    expect($hooksProperty)->toBe([
        'pre-commit' => [
            'composer lint -- --test',
        ],
        'pre-push' => [
            'composer lint -- --test',
            'composer stan',
            'composer types',
            'composer test',
        ],
    ]);

})->with([
    '{ "disabled": [], "hooks": { "pre-commit": [ "composer lint -- --test" ], "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
]);

it('others valid json', function ($responseFileContent) {

    $pathFile = '/tmp/whisky.json';

    File::shouldReceive('get')
        ->byDefault()
        ->with($pathFile)
        ->andReturn($responseFileContent);

    $config = FileJson::make($pathFile)->read();
    expect($config)->toBeArray();
    $disabledProperty = data_get($config, 'disabled', []);
    expect($disabledProperty)->toBeArray();
    $hooksProperty = data_get($config, 'hooks', []);
    expect($hooksProperty)->toBeArray();

})->with([
    '{ "hooks": {  } }',
    '{ "hooks": { "pre-commit": [ "composer lint -- --test" ] } }',
    '{ "disabled": [ "pre-commit" ], "hooks": { "pre-commit": [ "composer lint -- --test" ] } }',
]);

it('invalid json without validation', function ($responseFileContent) {

    $pathFile = '/tmp/whisky.json';

    File::shouldReceive('get')
        ->byDefault()
        ->with($pathFile)
        ->andReturn($responseFileContent);

    $config = FileJson::make($pathFile)->read(false);
    expect($config)->toBeArray();
    $disabledProperty = data_get($config, 'disabled', []);
    expect($disabledProperty)->toBeArray();
    // expect($disabledProperty)->toBe([]);
    $hooksProperty = data_get($config, 'hooks', []);
    expect($hooksProperty)->toBeArray();

})->with([
    '{ "foo": "bar", "disabled": [ "post-push" ], "hooks": { "post-push": [ "echo post-push" ], "pre-commit": { "command": "composer lint -- --test" }, "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
]);
