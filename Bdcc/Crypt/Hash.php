<?php

namespace Bdcc\Crypt;

/**
 * Bdcc\Crypt\Hash
 *
 * This class provides utility methods for encrypting
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 */

class Hash
{
    /**
     * Default hash algo
     */
    private static $algo = 'sha512';

    /**
     * Number of hash rounds
     */
    private static $rounds = 20;

    /**
     * Hashes password using salt provided (or generates new salt if non provided)
     *
     * @param   string  $password   Password to be hashed
     * @param   string  $salt       Salt to be used with password
     * @return  StdClass
     */
    public static function hashPassword($password, $salt = null)
    {
        $ret = new \StdClass();

        if (is_null($salt)) {
            $salt = self::generateSalt();
        }

        $ret->salt = $salt;

        // First mix password and salt
        $data = $password.$salt;

        for ($i=0; $i < self::$rounds; $i++) { 
            $data = hash(self::$algo, $data);
        }

        $ret->hash = $data;

        return $ret;
    }

    /**
     * Hashes an object using salt provided
     *
     * @param   string  $hash   hash an object
     * @param   string  $salt   Salt to be used with object
     * @param   integer $rounds The number of rounds to hash it by
     * @return  StdClass
     */
    public static function hash($hash, $salt = null, $rounds = 0)
    {
        $ret = new \StdClass();

        $ret->salt = $salt;

        // First mix password and salt
        $data = serialize($hash).$salt;

        for ($i=0; $i <= $rounds; $i++) { 
            $data = hash(self::$algo, $data);
        }

        $ret->hash = $data;

        return $ret;
    }

    /**
     * Generates salt
     *
     * @param   integer     $lenght     Salt length
     * @return  string
     */
    public static function generateSalt($lenght = 64)
    {
        $salt   = '';
        $chars = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'Q',
            'W',
            'V',
            'X',
            'Y',
            'Z',
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
        );

        for ($i=0; $i < $lenght; $i++) { 
            $salt .= $chars[rand(0, count($chars) - 1)];
        }

        return $salt;
    }
}
