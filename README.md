# Digitonic Queues

[![Latest Version on Packagist](https://img.shields.io/packagist/v/digitonic/queues.svg?style=flat-square)](https://packagist.org/packages/digitonic/queues)
[![Build Status](https://travis-ci.com/digitonic/queues.svg?branch=master)](https://travis-ci.com/digitonic/queues)
[![Total Downloads](https://img.shields.io/packagist/dt/digitonic/queues.svg?style=flat-square)](https://packagist.org/packages/digitonic/queues)

This package will add a centralized queue class which can be altered via environment variables.

## Installation

You can install the package via composer:

```bash
composer require digitonic/queues
```

## Usage

### Install Queue command

```bash
$ php artisan digitonic:queues:install
```

### Publish Queues to Docker Compose (Optional)

```bash
$ php artisan digitonic:queues:publish
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email steven@digitonic.co.uk instead of using the issue tracker.

## Credits

- [Steven Richardson](https://github.com/digitonic)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
