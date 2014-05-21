<?php

/**
* Bradley Dyer Bdcc test bootstrap.
* @author Kris Rybak kris.rybak@bradleydyer.com>
* @package icc
*/
//Set the include path to the classes directory
set_include_path(get_include_path() .
    PATH_SEPARATOR . '/home/sites/BDCC'
);

// Load local classes
require_once '/home/sites/BDCC/Bdcc/Autoloader.php';
Autoloader::enable();
