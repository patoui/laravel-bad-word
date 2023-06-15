# Laravel Bad Word

This package adds a configurable "bad_word" library with a validation rule and static methods to check for profanity and any words you want to add to check against!

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

### Validation

For all languages use the following

```php
'field_name' => 'bad_word'
```

For a specific language(s), the syntax is as shown in the snippet below (you may select any array key found in the config)

```php
'field_name' => 'bad_word:en,fr' // english and french only
```

### Static Methods

Add the following to any class
```
use Patoui\LaravelBadWord\Util\BadWordHelper;
```
Use either of these methods below
```
BadWordHelper::hasBadWords($string); //returns boolean
BadWordHelper::filterBadWords($string); //returns filtered string

// can also specify locale(s)
BadWordHelper::hasBadWords($string, ['en']);
BadWordHelper::filterBadWords($string, ['en', 'fr']);
```


## Configuration

You may publish the configuration file to `config/bad-word.php`

```
php artisan vendor:publish --provider="Patoui\LaravelBadWord\BadWordServiceProvider"
```

### Language Keys
You may add your own language keys as needed

```
'dothraki' => ['Mel ase']

// can be referenced in validation
'field_name' => 'bad_word:dothraki'

// can be referenced in static methods
BadWordHelper::hasBadWords($string, ['dothraki']);
BadWordHelper::filterBadWords($string, ['dothraki']);
```

### Strings vs Words
Each locale can specify a 'words' key and a 'strings' key.
- 'strings' match if they appear anywhere in the message
- 'words' only match if they appear as a whole word

If these keys are not specified for any locale, the system will handle any entries as 'words'

Examples
- 'Laguna' would fail if the string 'gun' were in the 'strings' list
- 'assess' would fail if the string 'ass' were in the 'strings' list
- 'Laguna' and 'Assess' would pass even if 'gun' or 'ass' are in the 'words' list


### Validation Message
Be sure to add a validation message to your `validation.php` file

```php
'bad_word' => 'The :attribute cannot contain bad words.',
```

## Contributions

Any contributions are welcome!
