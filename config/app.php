<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => 'Whisky',

    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    |
    | This value determines the "version" your application is currently running
    | in. You may want to follow the "Semantic Versioning" - Given a version
    | number MAJOR.MINOR.PATCH when an update happens: https://semver.org.
    |
    */

    'version' => app('git.version'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. This can be overridden using
    | the global command line "--env" option when calling commands.
    |
    */

    'env' => 'development',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [
        App\Providers\AppServiceProvider::class,
    ],



    // This will have to be changed if you have published the scripts directory
    // `php artisan vendor:publish --tag=laravel-git-hooks-scripts`
    'scripts_dir' => 'vendor/projektgopher/whisky/scripts',

    'disabled' => [
        'pre-push',
    ],
    

    /**
     * Git hooks configuration
     *
     * Scripts listed here can be found in the `vendor/projektgopher/laravel-git-hooks/scripts` directory.
     * Any single-line terminal command can be used here as well by prepending it was an '@' symbol.
     *
     * @example '@php artisan test'
     * @example '@npm run cypress'
     *
     * After installation, a hook's configuration can be
     * tested by running `git hook run <hook-name>`.
     * @example `git hook run pre-commit`
     */

    'hooks' => [
        /**
         * pre-commit
         *
         * This hook is invoked by git-commit, and can be bypassed with the `--no-verify`
         * option. It is invoked before obtaining the proposed commit log message and
         * making a commit. Exiting with a non-zero status from this script causes
         * the git commit command to abort before creating a commit.
         *
         * @see https://git-scm.com/docs/githooks#_pre_commit
         */
        'pre-commit' => [
            'pint-staged',
            'git-add-staged',
            'phpstan-analyse-staged',
        ],

        /**
         * pre-push
         *
         * This hook is invoked by git-push and can be used to prevent a push from taking place.
         * It is called with two parameters which provide the name and location of the destination
         * remote, if a named remote is not being used both values will be the same.
         *
         * If this hook exits with a non-zero status, git push will abort without
         * pushing anything. Information about why the push is rejected may be
         * sent to the user by writing to standard error.
         *
         * @see https://git-scm.com/docs/githooks#_pre_push
         */
        'pre-push' => [
            // 'php-artisan-test',
            '@php artisan test',
        ],
    ],

];
