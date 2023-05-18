# Manage project-wide git hooks in a simple way

[![Latest Version on Packagist](https://img.shields.io/packagist/v/projektgopher/whisky.svg?style=flat-square)](https://packagist.org/packages/projektgopher/whisky)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/projektgopher/whisky.svg?style=flat-square)](https://packagist.org/packages/projektgopher/whisky)

Keeping git hooks in sync across a whole team can have some issues. This cli tool aims to solve that.

## Installation

You can install the package via composer:

```bash
composer require projektgopher/whisky
./vendor/bin/whisky install
```

This will create a `whisky.json` file in your project root:

```json
{
  "scriptsDir": "./scripts",
  "disabled": [],
  "hooks": {
    "pre-commit": [
      "@./vendor/bin/pint --dirty"
    ],
    "pre-push": [
      "@php artisan test"
    ]
  }
}
```

> **Note** by adding `alias whisky=./vendor/bin/whisky` to your bash.rc file, you can shorten the length of the command.

## Usage

TODO

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

