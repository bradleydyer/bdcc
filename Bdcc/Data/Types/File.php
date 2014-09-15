<?php

namespace Bdcc\Data\Types;

/**
 * Bdcc\Data\Types\File
 *
 * @author Kris Rybak <kris.rybak@bradleydyer.com>
 */
class File {
    /**
     * Returns type of the file based on mimeType
     *
     * @return string|null  Type of the file or null if undetermined
     */
    public static function getTypeByMimeType($mimeType) {
        $typeSpec = explode('/', $mimeType);

        if (!empty($typeSpec)) {
            return $typeSpec[0];
        }

        return null;
    }
}
