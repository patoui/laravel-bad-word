<?php

namespace Patoui\LaravelBadWord\Validation;

use Illuminate\Support\Str;

class BadWord
{
    /**
     * Validates for bad words.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @param  array  $parameters
     * @param  object $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        if (!is_string($value)) {
            return true;
        }

        return !Str::contains($value, config('bad-word'));
    }
}
