<?php

namespace Bdcc;

use Bdcc\Exception as Bdcc_Exception;

/**
 * Interface implemented by a parser
 *
 * @author kris Rybak <kris.rybak@bradleydyer.com>
 */
interface ParserInterface
{
    /**
     * Parses data and returns the value
     *
     * @param   mixed       $data       Data to be parsed
     * @return  mixed       The value transformed
     * @throws  Bdcc_Exception when parse fails
     */
    public static function parse($data);
}
