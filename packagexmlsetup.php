<?php
/**
 * Extra package.xml settings such as dependencies.
 * More information: http://pear.php.net/manual/en/pyrus.commands.make.php#pyrus.commands.make.packagexmlsetup
 */
$package->dependencies['required']->package['pear.php.net/HTTP_Request2']
    ->save();
