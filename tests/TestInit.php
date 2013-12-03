<?php
/**
 * @ignore
 */
require_once 'PHPUnit/Autoload.php';

require_once __DIR__ . '/PEAR2/Services/NYTimes/Test/TestCase.php';

/**
 * testAutoloader
 *
 * @param string $className The class to load.
 *
 * @return boolean
 */
function testAutoloader($className)
{
    if (substr($className, 0, 22) == 'PEAR2\Services\NYTimes') {

        $file  = str_replace("\\", '/', $className);
        $file .= '.php';

        $path = realpath(__DIR__ . '/../src');

        return include $path . '/' . $file;
    }
    if (substr($className, 0, 6) == 'HTTP_R') {
        return include str_replace('_', '/', $className) . '.php';
    }
    return false;
}
spl_autoload_register('testAutoloader');
