<?php

namespace Bdcc\String;

/**
 * StopWords Class
 *
 * Provides methods to work with stop words that are words which are filtered out prior to, or after, processing of natural language data.
 * @author Kris Rybak kris.rybak@bradleydyer.com
 */
class StopWords
{
    /**
     * Lits of basic stop words
     *
     * @static  array
     */
    public static $basicStopWords = array(
        'and',
        'as',
        'at',
        'is',
        'on',
        'the',
        'which',
    );

    /**
     * Takes array of words and array of stop words and removes stop words from the haystack
     *
     * @param   array       $haystack       Array of words to search on
     * @param   array       $stopWords      Array of stop words to remove
     * @return  array                       Clean array of words
     */
    public static function cleanUp(array $haystack = array(), array $stopWords = array()) {
        return array_diff($haystack, $stopWords);
    }

    /**
     * Takes array of words and array of stop words and removes 
     * basic stop words form array. Can remove additional stop words specified
     * in second parameter
     *
     * @param   array       $haystack       Array of words to search on
     * @param   array       $stopWords      Array of stop words to remove
     * @return  array                       Clean array of words
     */
    public static function basicCleanUp(array $haystack = array(), array $stopWords = array())
    {
        return self::cleanUp($haystack, array_merge(self::$basicStopWords, $stopWords));
    }
}
