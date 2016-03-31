<?php

namespace Bdcc\Auth\App;

use Bdcc\Crypt\Hash;

/**
 * Bdcc\Auth\App\Token
 *
 * BradleyDyer implementation of digest token for applications
 * @author Kris Rybak kris.rybak@bradleydyer.com
 */
class Token
{
    public static $defaultRealm = 'bd-auth-app-token';

    /**
     * @static  List of properties and setters to use when decoding authorisation header
     */
    public static $headerMap = array(
        'App'       => 'setApp',
        'Nonce'     => 'setNonce',
        'Rounds'    => 'setRounds',
        'Realm'     => 'setRealm',
    );

    private $app;

    private $key;

    private $nonce;

    private $rounds;

    private $realm;

    public function __construct($app = null, $key = null, $nonce = null, $rounds = 0)
    {
        $this->app      = $app;
        $this->key      = $key;
        $this->nonce    = $nonce;
        $this->rounds   = $rounds;
        $this->realm    = self::$defaultRealm;
    }

    public function getApp()
    {
        return $this->app;
    }

    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    public function getNonce()
    {
        return $this->nonce;
    }

    public function setNonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

    public function getRounds()
    {
        return $this->rounds;
    }

    public function setRounds($rounds)
    {
        $this->rounds = $rounds;

        return $this;
    }

    public function getRealm()
    {
        return $this->realm;
    }

    public function setRealm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Generates authorization header
     *
     * @return  string
     */
    public function generateHeader()
    {
        return sprintf(
            'Token="%s", App="%s", Nonce="%s", Rounds="%s", Realm="%s"',
            $this->getToken(),
            $this->getApp(),
            $this->getNonce(),
            $this->getRounds(),
            $this->getRealm()
        );
    }

    /**
     * Get HA1
     *
     * Generates object with hash property representing Token Digest HA1 with exception
     * that it uses sha256 instead MD5 as a hashing algorithm.
     * @link    https://en.wikipedia.org/wiki/Digest_access_authentication#Overview
     * @return  StdClass
     */
    private function getHa1()
    {
        return Hash::hash256($this->getKey() . ':' . $this->getRealm() . ':' . $this->getApp());
    }

    /**
     * Get token
     *
     * @return  string
     */
    public function getToken()
    {
        return $this->generateToken()->hash;
    }

    /**
     * Generates token
     *
     * @return  StdClass
     */
    private function generateToken()
    {
        return Hash::hash256(self::getHa1()->hash, $this->getNonce(), $this->getRounds());
    }

    /**
     * Generates random nonce
     *
     * @return  string
     */
    public static function generateNonce()
    {
        return Hash::generateSalt();
    }

    /**
     * Loads Token properties from $header.
     * This helper method makes it easier to access and manipulate
     * keys and values of the header.
     *
     * @param   string  $header     Authorisation header sent by client
     * @return  StdClass
     */
    public function fromHeader($header)
    {
        $object = new \StdClass;

        if (is_string($header)) {
            // Get parts for each of header keys
            $parts = explode(", ", $header);

            // Loop through and get pair of key and value
            foreach ($parts as $pair) {
                $start  = strpos($pair, '"');
                $end    = strrpos($pair, '"');

                $key    = substr($pair, 0, $start - 1);
                $value  = substr($pair, $start + 1, $end - $start - 1);

                $object->$key = $value;
            }

            // Now that we have object let's load data into header
            foreach (self::$headerMap as $key => $setter) {
                if (isset($object->$key)) {
                    $this->$setter($object->$key);
                }
            }
        }

        return $object;
    }

    /**
     * Checks if given header is valid when used with supplied $key.
     *
     * @param   string  $header     Header to validate
     * @param   string  $key        Key to use for $header validation
     * @return  boolean             True if valid pair, otherwise false
     */
    public function validate($header, $key)
    {
        $header = $this->fromHeader($header);
        $this->setKey($key);

        if (isset($header->Token)) {
            if ($header->Token === $this->getToken()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Alias of validate()
     *
     * @param   string  $header     Header to validate
     * @param   string  $key        Key to use for $header validation
     * @return  boolean             True if valid pair, otherwise false
     */
    public function isValid($header, $key)
    {
        return $this->validate($header, $key);
    }
}
