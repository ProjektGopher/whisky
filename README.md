<p align="center"><img src="https://github.com/ProjektGopher/whisky/raw/HEAD/art/logo.svg" width="75%" alt="Whisky Logo"></p>

<p align="center"><img src="https://github.com/ProjektGopher/whisky/raw/HEAD/art/example.png" width="75%" alt="Whisky Terminal Example"></p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/projektgopher/whisky.svg?style=flat-square)](https://packagist.org/packages/projektgopher/whisky)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/projektgopher/whisky.svg?style=flat-square)](https://packagist.org/packages/projektgopher/whisky)


## Introduction
Whisky is the simplest, **framework agnostic**, CLI tool for managing and enforcing a project's git hooks across an entire team.


## Installation
Whisky's **only** dependency is `php^8.1`.

You can install the package via composer:

```bash
composer require --dev projektgopher/whisky
./vendor/bin/whisky install
```

> **Note** Whisky does **not** currently work as expected when installed _globally_.

This will create a `whisky.json` file in your project root:

```json
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


## Usage
For a complete list of supported git hooks, see the [Git Documentation](https://git-scm.com/docs/githooks#_hooks).

Each item under a given hook will be execuated in order as-is in the terminal.
For anything more complicated than simple terminal commands it's recommended to create a
`scripts` directory in your project root, and reference those here instad.

```sh
# ./scripts/git-add-staged
#!/bin/bash

# Create a list of all staged files
# filter out deleted files
STAGED_FILES=$(git diff --name-only --cached --diff-filter=d)

# Pass that list of files to the following commands
if [ -n "$STAGED_FILES" ]; then
    # Re-stage the files that were just linted
    git add $STAGED_FILES
fi
```

```json
// whisky.json
...
    "pre-push": [
      "composer lint",
      "./scripts/git-add-staged"
    ]
...
```

> **Note** When doing this, make sure any scripts referenced are **executable**:
```bash
chmod +x ./scripts/*
```

### Skipping Hooks
Sometimes you need to commit or push changes without running your git hooks, like when handing off work to another computer.
This can usually be done using git's _native_ `--no-verify` flag.
```bash
git commit -m "wip" --no-verify
```

However, some git actions don't support this flag, like `git merge --continue`.
In this case, running the following command will have the exact same effect.
```bash
./vendor/bin/whisky skip-once
``` 

> **Note** by adding `alias whisky=./vendor/bin/whisky` to your `bash.rc` file, you can shorten the length of this command.

### Disabling Hooks
Adding a hook's name to the `disabled` array in your `whisky.json` will disable the hook from running.
This can be useful when building out a workflow that isn't ready for the rest of the team yet.

> **Note** The `disabled` array is only read on _installation_, therefor any changes **must** be followed by `./vendor/bin/whisky install`.


## Security
> **Warning** all hooks are **evaluated as-is** in the terminal. Keep this in mind when committing anything involving changes to your `whisky.json`.


## Testing
```bash
composer test
```


## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Security Vulnerabilities
Please review [our security policy](../../security/policy) on how to report security vulnerabilities.


## Credits
A **big** "Thank You" to [EXACTsports](https://github.com/EXACTsports) for supporting the development of this package.

- [Len Woodward](https://github.com/ProjektGopher)
- [All Contributors](../../contributors)


## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
