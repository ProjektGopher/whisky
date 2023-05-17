<a href="https://supportukrainenow.org/"><img src="https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner-direct.svg" width="100%"></a>

------

<p align="center">
    <img title="Laravel Zero" height="100" src="https://raw.githubusercontent.com/laravel-zero/docs/master/images/logo/laravel-zero-readme.png" />
</p>

<p align="center">
  <a href="https://github.com/laravel-zero/framework/actions"><img src="https://github.com/laravel-zero/laravel-zero/actions/workflows/tests.yml/badge.svg" alt="Build Status"></img></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/dt/laravel-zero/framework.svg" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/v/laravel-zero/framework.svg?label=stable" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel-zero/framework"><img src="https://img.shields.io/packagist/l/laravel-zero/framework.svg" alt="License"></a>
</p>

<h4> <center>This is a <bold>community project</bold> and not an official Laravel one </center></h4>

Laravel Zero was created by [Nuno Maduro](https://github.com/nunomaduro) and [Owen Voke](https://github.com/owenvoke), and is a micro-framework that provides an elegant starting point for your console application. It is an **unofficial** and customized version of Laravel optimized for building command-line applications.

- Built on top of the [Laravel](https://laravel.com) components.
- Optional installation of Laravel [Eloquent](https://laravel-zero.com/docs/database/), Laravel [Logging](https://laravel-zero.com/docs/logging/) and many others.
- Supports interactive [menus](https://laravel-zero.com/docs/build-interactive-menus/) and [desktop notifications](https://laravel-zero.com/docs/send-desktop-notifications/) on Linux, Windows & MacOS.
- Ships with a [Scheduler](https://laravel-zero.com/docs/task-scheduling/) and  a [Standalone Compiler](https://laravel-zero.com/docs/build-a-standalone-application/).
- Integration with [Collision](https://github.com/nunomaduro/collision) - Beautiful error reporting

------

## Documentation

For full documentation, visit [laravel-zero.com](https://laravel-zero.com/).

## Support the development
**Do you like this project? Support it by donating**

- PayPal: [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=66BYDWAT92N6L)
- Patreon: [Donate](https://www.patreon.com/nunomaduro)

## License

Laravel Zero is an open-source software licensed under the MIT license.






# Manage project-wide git hooks in a simple way

[![Latest Version on Packagist](https://img.shields.io/packagist/v/projektgopher/whisky.svg?style=flat-square)](https://packagist.org/packages/projektgopher/whisky)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/projektgopher/whisky/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/projektgopher/whisky/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/projektgopher/whisky.svg?style=flat-square)](https://packagist.org/packages/projektgopher/whisky)

Keeping git hooks in sync across a while team can have some issues. This packages aims to solve that.

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

