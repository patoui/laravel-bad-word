<?php

namespace Patoui\LaravelBadWord\Validation;

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

        $words = count($parameters) === 0 ?
            config('bad-word') :
            array_only(config('bad-word'), $parameters);

        return !str_contains($value, array_flatten($words));
    }
}
