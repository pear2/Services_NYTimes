<?php
/**
 * @ignore
 */
require_once 'PHPUnit/Autoload.php';

/**
 * testAutoloader
 *
 * @param string $className
 *
 * @return boolean
 */
function testAutoloader($className) {
    if (substr($className, 0, 22) == 'PEAR2\Services\NYTimes') {

        $file  = substr($className, 6);
        $file  = str_replace("\\", '/', $file);
        $file .= '.php';

        $path = realpath(__DIR__ . '/../src');

        return include $path . '/' . $file;
    }
    return false;
}
spl_autoload_register('testAutoloader');
