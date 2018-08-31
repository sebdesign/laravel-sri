# Laravel SRI

Subresource Integrity (SRI) package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sebdesign/laravel-sri.svg)](https://packagist.org/packages/sebdesign/laravel-sri)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/sebdesign/laravel-sri/master.svg)](https://travis-ci.org/sebdesign/laravel-sri)
[![StyleCI](https://github.styleci.io/repos/71842300/shield)](https://github.styleci.io/repos/71842300)

Reference and generate [Subresource Integrity (SRI)](https://www.w3.org/TR/SRI/) hashes from your Laravel Elixir asset pipeline.

## Installation

You can install the package via composer:

```bash
composer require sebdesign/laravel-sri
```

Laravel 5.5 uses Package Auto-Discovery, so doesn't require you to manually add the service provider.
If you don't use auto-discovery or you are using an older version, you must add the following:

```php
// config/app.php
'providers' => [
    Sebdesign\SRI\SubresourceIntegrityServiceProvider::class,
];
```

## Usage

This package is aimed to reference SRI hashes for `css` and `js` files from a `sri.json` file in your `/public` folder. In order to generate this file, see the [laravel-elixir-sri](https://github.com/sebdesign/laravel-elixir-sri) repository.

To reference the generated hashes from the `sri.json` in your views, you may use the `integrity` helper function with the name of the file you are using in your `elixir` or `asset` function.

As a fallback, if the given file is not found in the `sri.json`, **it will generate the appropriate hashes on the fly** for your convenience.

```php
// Use with elixir() function
<link
    rel="stylesheet"
    href="{{ elixir('css/app.css') }}"
    integrity="{{ integrity('css/app.css') }}"
    crossorigin="anonymous">

// Use with asset() function
<script
    src="{{ asset('js/app.js') }}"
    integrity="{{ integrity('js/app.js') }}" 
    crossorigin="anonymous">
</script>
```

If you have set the output folder for the `sri.json` in a different location in your Gulpfile, you can specify its `path` on the `config/sri.php`.

```php
// config/sri.php
'path' => '/public/assets',
```

You can also override the config options by passing an array as a second argument on the `integrity` helper function:

```php
// Use different hash algorithm
<link
    rel="stylesheet"
    href="{{ elixir('css/app.css') }}"
    integrity="{{ integrity('css/app.css', ['algorithms' => ['sha384']]) }}" 
    crossorigin="anonymous">
```

## Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security related issues, please email info@sebdesign.eu instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
