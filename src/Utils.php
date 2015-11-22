<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

class Utils
{
    /**
     * Look up the three until finding a file.
     *
     * @param string $file
     * @param string $from
     *
     * @return string
     */
    public static function find($file, $from = __DIR__)
    {
        $path = $from;
        while (!file_exists($path.DIRECTORY_SEPARATOR.$file)) {
            $path .= '/..';
        }

        return realpath($path.DIRECTORY_SEPARATOR.$file);
    }
}