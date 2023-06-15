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
        if(app()->version() < 5.6) {
            collect($bad_words->strings)->each(function($string) use ($message) {
                if (str_contains($message, $string)) return true;
            });
        } else {
            if (Str::contains($message, $bad_words->strings->toArray())) return true;
        }

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

            //filter for strings
            collect($bad_words->strings)->each(function($string) use (&$message) {
                $message = str_ireplace($string, str_repeat("*", strlen($string)), $message);
            });

            //filter for words
            collect($bad_words->words)->each(function($word) use (&$message) {
                //word must be preceded and followed by a space, punctuation, or the start/end of the message
                //replace word with '****' of same length
                $message = preg_replace('/\b'.$word.'\b/i', str_repeat("*", strlen($word)), $message);
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
            if(app()->version() < 5.6) {
                $filtered_bad_words = array_only(config('bad-word'), self::$filter);
            } else {
                $filtered_bad_words = Arr::only(config('bad-word'), self::$filter);
            }

            //loop through the locales
            foreach($filtered_bad_words as $lang=>$data) {
                //process the keys
                foreach($keys as $key) {
                    if (array_key_exists($key, $data)) {
                        $bad_words->$key = $bad_words->$key->merge($data[$key]);
                    }
                }
                //handle any unkeyed words
                if(app()->version() < 5.6) {
                    $unkeyed = array_except($data, $keys);
                } else {
                    $unkeyed = Arr::except($data, $keys);
                }
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