<?php

namespace Patoui\LaravelBadWord\Util;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BadWordHelper
{
    private static $bad_words;
    private static $filter;

    /**
     * Check for the presence of prohibited keywords
     *
     * @param string $message
     * @param array $filter
     * @return boolean
     */
    public static function hasBadWords($message, $filter = []) {
        $bad_words = self::getBadWords($filter);

        $message = strtolower($message);

        //filter for strings first
        if (Str::contains($message, $bad_words->strings->toArray())) return true;

        //filter for words
        if(!$bad_words->words->every(function($word) use ($message) {
            //word must be preceded and followed by a space, punctuation, or the start/end of the message
            return !preg_match('/\b'.$word.'\b/i', $message);
        })) return true;

        //if we got here...all good
        return false;
    }

    /**
     * Filter Prohibited keywords
     *
     * @param string $message
     * @param array $filter
     * @return string
     */
    public static function filterBadWords($message, $filter = []) {
        if(static::hasBadWords($message)) {
            $bad_words = self::getBadWords($filter);

            $replacement_string = getenv('LARAVEL_BAD_WORD_REPLACEMENT_STRING', '');

            //filter for strings
            collect($bad_words->strings)->each(function($string) use (&$message, $replacement_string) {
                $replace = (1 === strlen($replacement_string)) ? str_repeat($replacement_string, strlen($string)) : $replacement_string ;
                $message = str_ireplace($string, $replace, $message);
            });

            //filter for words
            collect($bad_words->words)->each(function($word) use (&$message, $replacement_string) {
                $replace = (strlen($replacement_string) == 1) ? str_repeat($replacement_string, strlen($word)) : $replacement_string ;
                //word must be preceded and followed by a space, punctuation, or the start/end of the message
                $message = preg_replace('/\b'.$word.'\b/i', $replace, $message);
            });
        }
        return $message;
    }

    /**
     * Get the list of prohibited keywords
     *
     * @param array $filter
     * @return object
     */
    private static function getBadWords($filter=[]) {
        if(empty($filter)) $filter = [config('app.locale')];
        if(empty(self::$bad_words) || empty(self::$filter) || $filter != self::$filter) {
            //cache the filter
            self::$filter = $filter;

            //set up the holder
            $keys = ['strings','words'];
            $bad_words = (object) [];
            foreach($keys as $key) $bad_words->$key = collect([]);

            //get the filtered array
            $filtered_bad_words = Arr::only(config('bad-word'), self::$filter);

            //loop through the locales
            foreach($filtered_bad_words as $lang=>$data) {
                //process the keys
                foreach($keys as $key) {
                    if (array_key_exists($key, $data)) {
                        $bad_words->$key = $bad_words->$key->merge($data[$key]);
                    }
                }
                //handle any unkeyed words
                $unkeyed = Arr::except($data, $keys);
                if(!empty($unkeyed)) {
                    $bad_words->words = $bad_words->words->merge($unkeyed);
                }
            }

            //post-process the arrays
            foreach($keys as $key) {
                $bad_words->$key = $bad_words->$key->flatten();
                $bad_words->$key->transform(function ($item, $k) {
                    return strtolower($item);
                });
                $bad_words->$key = $bad_words->$key->unique();
                $bad_words->$key->sortByDesc(function($string, $k) {
                    return strlen($string);
                });
            }

            //cache it
            self::$bad_words = $bad_words;
        }
        return self::$bad_words;
    }

}