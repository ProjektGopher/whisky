<?php

use Exception;
use Illuminate\Support\Facades\File;
use ProjektGopher\Whisky\FileJson;

beforeEach(function () {
    $this->test_path = '/tmp/whisky.json';
});

it('fails if json is invalid', function ($test_json) {
    File::shouldReceive('get')
        ->byDefault()
        ->with($this->test_path)
        ->andReturn($test_json);

    $this->expectException(Exception::class);
    // $this->expectExceptionMessage('Invalid JSON: in '.$this->test_path);
    $this->expectExceptionMessageMatches('/Invalid JSON: .+ in '.str_replace('/', '\/', $this->test_path).'/');

    FileJson::make($this->test_path)->read();
})->with([
    'foo',                  // no json => Syntax error
    '{ foo: "var" }',       // missing quotes => Syntax error
    '{ "foo" = "var" }',    // wrong operator => Syntax error
    '{ "foo", "var" }',     // wrong separator => Syntax error
    '{ "foo": "var", "var": "foo", }', // trailing comma => Syntax error
    '{ "foo": "var }',      // missing closing quote => Control character error, possibly incorrectly encoded
]);

it('fails if json is valid but is not an object', function ($test_json) {
    File::shouldReceive('get')
        ->byDefault()
        ->with($this->test_path)
        ->andReturn($test_json);

    $this->expectException(Exception::class);
    // $this->expectExceptionMessage('Invalid JSON schema: in '.$this->test_path);
    $this->expectExceptionMessageMatches('/Invalid JSON schema: .+ in '.str_replace('/', '\/', $this->test_path).'/');

    FileJson::make($this->test_path)->read();
})->with([
    'true',     // bool
    'false',    // bool
    'null',     // null
    '"foo"',    // string
    '1',        // int
    '1.1',      // float
    '[]',       // array
]);

it('fails if json is valid but does not satisfy schema', function ($test_json) {
    File::shouldReceive('get')
        ->byDefault()
        ->with($this->test_path)
        ->andReturn($test_json);

    $this->expectException(Exception::class);
    // $this->expectExceptionMessage('Invalid JSON schema: in '.$this->test_path);
    $this->expectExceptionMessageMatches('/Invalid JSON schema: .+ in '.str_replace('/', '\/', $this->test_path).'/');

    FileJson::make($this->test_path)->read();
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

it('validates json', function ($test_json) {
    File::shouldReceive('get')
        ->byDefault()
        ->with($this->test_path)
        ->andReturn($test_json);

    $config = FileJson::make($this->test_path)->read();

    expect($config)->toBeArray();
    expect(data_get($config, 'disabled'))
        ->toBeArray()
        ->toBe([]);
    expect(data_get($config, 'hooks'))
        ->toBeArray()
        ->toBe([
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

it('accepts other valid json', function ($test_json) {
    File::shouldReceive('get')
        ->byDefault()
        ->with($this->test_path)
        ->andReturn($test_json);

    $config = FileJson::make($this->test_path)->read();

    expect($config)->toBeArray();
    expect(data_get($config, 'disabled', []))->toBeArray();
    expect(data_get($config, 'hooks'))->toBeArray();
})->with([
    '{ "hooks": {  } }',
    '{ "hooks": { "pre-commit": [ "composer lint -- --test" ] } }',
    '{ "disabled": [ "pre-commit" ], "hooks": { "pre-commit": [ "composer lint -- --test" ] } }',
]);

it('invalid json without validation', function ($test_json) {
    File::shouldReceive('get')
        ->byDefault()
        ->with($this->test_path)
        ->andReturn($test_json);

    $config = FileJson::make($this->test_path)->read(false);

    expect($config)->toBeArray();
    expect(data_get($config, 'disabled'))->toBeArray(); // ->toBe([]);
    expect(data_get($config, 'hooks'))->toBeArray();
})->with([
    '{ "foo": "bar", "disabled": [ "post-push" ], "hooks": { "post-push": [ "echo post-push" ], "pre-commit": { "command": "composer lint -- --test" }, "pre-push": [ "composer lint -- --test", "composer stan", "composer types", "composer test" ] } }',
]);
