# Laravel Twitch

## Still in development

[![Latest Version on Packagist](https://img.shields.io/packagist/v/farzai/twitch.svg?style=flat-square)](https://packagist.org/packages/farzai/twitch)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/farzai/laravel-twitch/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/farzai/laravel-twitch/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/farzai/laravel-twitch/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/farzai/laravel-twitch/actions?query=workflow%3AFix+PHP+code+style+issues+branch%3Amain)
[![codecov](https://codecov.io/gh/farzai/laravel-twitch/branch/main/graph/badge.svg)](https://codecov.io/gh/farzai/laravel-twitch)
[![Total Downloads](https://img.shields.io/packagist/dt/farzai/twitch.svg?style=flat-square)](https://packagist.org/packages/farzai/twitch)

## Description

Laravel Twitch is a package that provides an easy way to interact with the Twitch API using Laravel. It simplifies the process of making requests to the Twitch API and handling the responses. The package includes features such as authentication, making API requests, and handling pagination.

## Installation

To install the package, you can use Composer:

```bash
composer require farzai/twitch
```

The package requires PHP 8.1 or higher and the following dependencies:

- spatie/laravel-package-tools: ^1.14.0
- illuminate/contracts: ^10.0

## Usage

Here are some examples of how to use the package:

### Retrieving an Access Token

```php
use Farzai\Twitch\Authorizer;

$accessToken = Authorizer::retrieveAccessToken();
```

### Making API Requests

```php
use Farzai\Twitch\Builder;
use Farzai\Twitch\Models\Game;

$builder = new Builder(Game::class);
$games = $builder->search('Fortnite')->get();
```

## Configuration

To configure the package, you need to set up the necessary credentials in your `.env` file:

```env
TWITCH_CLIENT_ID=your-client-id
TWITCH_CLIENT_SECRET=your-client-secret
```

You can also publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --tag=twitch-config
```

## Contributing

If you would like to contribute to the package, please follow these guidelines:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Write tests for your changes.
4. Run the tests to ensure they pass.
5. Submit a pull request with a clear description of your changes.

If you find any issues or have any questions, feel free to open an issue on GitHub.

