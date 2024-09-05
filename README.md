<p align="center"><img src="https://github.com/ProjektGopher/whisky/raw/HEAD/art/logo.svg" width="75%" alt="Whisky Logo"></p>

<p align="center"><img src="https://github.com/ProjektGopher/whisky/raw/HEAD/art/example.png" width="75%" alt="Whisky Terminal Example"></p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/projektgopher/whisky.svg?style=flat-square)](https://packagist.org/packages/projektgopher/whisky)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/test.yml?branch=main&label=tests&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3Atest+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/pint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3Apint+branch%3Amain)
[![GitHub Static Analysis Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/larastan.yml?branch=main&label=static%20analysis&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3Alarastan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/projektgopher/whisky.svg?style=flat-square)](https://packagist.org/packages/projektgopher/whisky)


## Introduction
Whisky is the simplest, **framework agnostic**, CLI tool for managing and enforcing a php project's git hooks across an entire team.

Git hooks are a fantastic tool to ensure that code hitting version control satisfies your org's code quality standards. However, `.git/hooks` is not included in your git tree. This makes it impractical to have all contributors to a repository use the same checks with the same settings.


## Installation
Whisky's **only** dependency is `php^8.1`.

You can install the package via composer:

```bash
composer require --dev projektgopher/whisky
./vendor/bin/whisky install
```

This is the recommended method, as every developer on your project will have access to the tool.

### Global Installation
Whisky can be installed globally, however this means that any developer on your project will _also_ need it installed globally if they want to use it.

```bash
composer global require projektgopher/whisky
whisky install
```

If Whisky is installed both globally, and locally, on a project the version that's run will depend on how the command is invoked.


## Usage
The `install` command will create a `whisky.json` file in your project root:

```js
// whisky.json
{
  "disabled": [],
  "hooks": {
    "pre-commit": [
      "./vendor/bin/pint --dirty"
    ],
    "pre-push": [
      "php artisan test"
    ]
  }
}
```

For a complete list of supported git hooks, see the [Git Documentation](https://git-scm.com/docs/githooks#_hooks).

> [!CAUTION]
> all hooks are **evaluated as-is** in the terminal. Keep this in mind when committing anything involving changes to your `whisky.json`.

Adding or removing any **hooks** (_not_ individual commands) to your `whisky.json` file should be followed by `./vendor/bin/whisky update` to ensure that these changes are reflected in your `.git/hooks` directory.

### Automating Hook Updates
While we suggest leaving Whisky as an 'opt-in' tool, by adding a couple of Composer scripts we can _ensure_ consistent git hooks for all project contributors. This will **force** everyone on the project to use Whisky:

```js
// composer.json
// ...  
  "scripts": {
    "post-install-cmd": [
      "whisky update"
    ],
    "post-update-cmd": [
      "whisky update"
    ]
  }
// ...
```

### Skipping Hooks
Sometimes you need to commit or push changes without running your git hooks,
like when handing off work to another computer. This can usually be done
using git's _native_ `--no-verify` flag.
```bash
git commit -m "wip" --no-verify
```

However, some git actions don't support this flag, like `git merge --continue`.
In this case, running the following command will have the exact same effect.
```bash
./vendor/bin/whisky skip-once
``` 

> [!TIP] 
> by adding `alias whisky=./vendor/bin/whisky` to your `bash.rc` file, you can shorten the length of this command.

### Disabling Hooks
Adding a hook's name to the `disabled` array in your `whisky.json` will disable the hook from running.
This can be useful when building out a workflow that isn't ready for the rest of the team yet.


## Advanced Usage
For anything more complicated than simple terminal commands it's recommended to create a
`scripts` directory in your project root. This comes with the added benefit of allowing
you to run scripts written in _any_ language.

```js
// whisky.json
// ...
  "pre-push": [
    "composer lint",
    "rustc ./scripts/complicated_thing.rs"
  ]
// ...
```

> [!NOTE] 
> When doing this, make sure any scripts referenced are **executable**:
```bash
chmod +x ./scripts/*
```


## Testing
```bash
# Run test suite
composer test

# Test hook without having to make a dummy commit
git hook run pre-commit
```


## Troubleshooting
If you've installed Whisky **both** locally **and** globally, and your hooks are being run _twice_, try uninstalling whisky from your hooks for **one** of those installations.

```bash
# Remove global Whisky hooks, leaving the local ones,
# while keeping `whisky.json` in the project root.
whisky uninstall -n
```


## Contributing
> [!NOTE] 
> Don't build the binary when contributing. The binary will be built when a release is tagged.

Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.


## Security Vulnerabilities
Please review [our security policy](../../security/policy) on how to report security vulnerabilities.


## Credits
A **big** "Thank You" to [EXACTsports](https://github.com/EXACTsports) for supporting the development of this package.

- [Len Woodward](https://github.com/ProjektGopher)
- [All Contributors](../../contributors)


## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
