<?php
/**
 * @ignore
 */
require_once 'PHPUnit/Autoload.php';

if (file_exists(__DIR__ . '/config.php')) {
    include __DIR__ . '/config.php';
}

require_once __DIR__ . '/Services/NYTimes/TestCase.php';

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
    if (substr($className, 0, 6) == 'HTTP_R') {
        return include str_replace('_', '/', $className) . '.php';
    }
    return false;
}
spl_autoload_register('testAutoloader');
