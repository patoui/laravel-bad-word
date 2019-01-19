# Laravel Bad Word

This package adds a "bad_word" validation rule which check for profanity and any words you want to add to check against!

## Installation

This package can be used in Laravel 5.0 or higher.

You can install the package via composer:

```
composer require patoui/laravel-bad-word
```

In Laravel 5.5 the service provider will automatically get registered. For older versions of the framework, add the service provider in config/app.php file:

```
'providers' => [
    // ...
    Patoui\LaravelBadWord\BadWordServiceProvider::class,
];
```

## Usage

For all languages use the following

```php
'field_name' => 'bad_word'
```

For a specific language(s), the syntax is as shown in the snippet below (you may select any array key found in the config)

```php
'field_name' => 'bad_word:en,fr' // english and french only
```

## Configuration

To publish the configuration file to `config/bad-word.php`

```
php artisan vendor:publish --provider="Patoui\LaravelBadWord\BadWordServiceProvider"
```

You may add your own language keys as needed

```
'dothraki' => ['Mel ase']

// can be referenced in validation
'field_name' => 'bad_word:dothraki'
```

Be sure to add a validation message to your `validation.php` file

```php
'bad_word' => 'The :attribute cannot contain bad words.',
```

### Contributions

Any contributions are welcome!
