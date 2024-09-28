<?php

namespace ProjektGopher\Whisky;

use Illuminate\Support\Facades\File;
use Swaggest\JsonSchema\Context;
use Swaggest\JsonSchema\Schema;

class FileJson
{
    public function __construct(
        public string $path,
    ) {
        //
    }

    public static function make(string $path): FileJson
    {
        return new FileJson($path);
    }

    public function read(bool $validate = true): string|array|null
    {
        $content = json_decode(File::get($this->path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $msg = 'Invalid JSON';
            if (function_exists('json_last_error_msg')) {
                $msg .= ': '.json_last_error_msg();
                $msg .= ' in '.$this->path;
            }
            throw new \Exception($msg);
        }

        if ($validate) {
            try {
                $contentJson = json_decode(File::get($this->path));
                $this->validate($contentJson);
            } catch (\Exception $e) {
                $msg = 'Invalid JSON schema';
                $msg .= ': '.$e->getMessage();
                $msg .= ' in '.$this->path;
                throw new \Exception($msg);
            }
        }

        return $content;
    }

    /**
     * @param  \stdClass|array|string|int|float|bool|null  $content
     *
     * @throws \Exception
     */
    protected function validate(mixed $content): void
    {
        $options = new Context;
        // $options->version = 7;

        $schema = Schema::import($this->getSchemaValidation(), $options);

        $schema->in($content);
    }

    protected function getSchemaValidation(): ?\stdClass
    {
        // return json_decode(File::get(Whisky::base_path('resources/schemas/whisky.json')), false);
        $schema = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'properties' => [
                'disabled' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
                'hooks' => [
                    'type' => 'object',
                    'properties' => [],
                    'additionalProperties' => false,
                ],
                'prepend' => [
                    'type' => 'string',
                ],
            ],
            'required' => ['hooks'],
            'additionalProperties' => false,
        ];

        // TODO: Move this into const on the Hooks class.
        $availableHooks = [
            'pre-commit',
            'prepare-commit-msg',
            'commit-msg',
            'post-commit',
            'applypatch-msg',
            'pre-applypatch',
            'post-applypatch',
            'pre-rebase',
            'post-rewrite',
            'post-checkout',
            'post-merge',
            'pre-push',
            'pre-auto-gc',
        ];
        $schema['properties']['disabled']['items']['enum'] = $availableHooks;
        foreach ($availableHooks as $hook) {
            $schema['properties']['hooks']['properties'][$hook] = [
                'type' => 'array',
                'items' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'pattern' => '^(?!\s*$).+',
                ],
                'minItems' => 1,
            ];
        }

        return json_decode(json_encode($schema), false);
    }
}
