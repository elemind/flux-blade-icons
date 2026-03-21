# Use Blade Icons in Flux applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elemind/flux-blade-icons.svg?style=flat-square)](https://packagist.org/packages/elemind/flux-blade-icons)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/elemind/flux-blade-icons/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/elemind/flux-blade-icons/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/elemind/flux-blade-icons/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/elemind/flux-blade-icons/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elemind/flux-blade-icons.svg?style=flat-square)](https://packagist.org/packages/elemind/flux-blade-icons)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/flux-blade-icons.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/flux-blade-icons)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require elemind/flux-blade-icons
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="flux-blade-icons-config"
```

The published config file includes the default registry and a separate override section:

```php
return [
    'output_path' => resource_path('views/flux/icon'),
    'cache_ttl' => 86400,

    'default_icon_sets' => [
        // Built-in icon sets shipped with the package...
    ],

    'icon_sets' => [
        // Add custom sets or override an existing built-in key.
    ],
];
```

Use `icon_sets` to add your own packages or override one of the built-in keys without editing the package defaults.
If a custom package is not hosted on GitHub, or the GitHub API is temporarily unavailable, the import command falls back to manual icon entry and will show an error if the requested icon does not exist.

You can also clear the cached icon lists used by the importer:

```bash
php artisan flux:blade-icons:clear-cache
php artisan flux:blade-icons:clear-cache --set=blade-feather-icons
```

## Usage

Generate one or more Flux Blade icon components from the configured registry:

```bash
php artisan flux:blade-icons
php artisan flux:blade-icons academic-cap
php artisan flux:blade-icons academic-cap --set=heroicons
```

Clear the cached icon lists when you want to refresh remote package metadata:

```bash
php artisan flux:blade-icons:clear-cache
php artisan flux:blade-icons:clear-cache --set=blade-feather-icons
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Tiziano Zonta](https://github.com/tizianozonta)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
