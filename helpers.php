<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 * 
 */

function basePath($path = '')
{

    return __DIR__ . '/' . $path;   // __DIR__ returns the absolute path

}
