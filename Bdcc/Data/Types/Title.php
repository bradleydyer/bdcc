<?php

namespace Bdcc\Data\Types;

/**
 * Bdcc\Data\Types\Title
 *
 * @author Anton McCook <anton.mccook@bradleydyer.com>
 */
class Title {
    /**
     * Get an array of title options
     *
     * @return array
     */
    public static function getTitles() {
        return array(
            'Mr'        => 'Mr',
            'Mrs'       => 'Mrs',
            'Ms'        => 'Ms',
            'Miss'      => 'Miss',
            'Dr.'       => 'Dr.',
        );
    }
}
