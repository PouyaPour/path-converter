<?php


namespace seosazi\PathConverter;


/**
 * Convert file paths.
 *
 * Please report bugs on https://github.com/seosazi/path-converter/issues
 *
 * @author Pouya Pormohamad <p.pormohamad@gmail.com>
 * @copyright Copyright (c) 2020, Pouya Pormohamad. All rights reserved
 * @license MIT License
 */

interface ConverterInterface
{
    /**
     * Convert paths.
     *
     * @param string $path
     * @return string The new path
     */
    public function convert($path);
}