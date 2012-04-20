<?php
/**
 * PEAR2\Services\NYTimes\MainTest
 *
 * PHP version 5
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id$
 * @link      https://github.com/pear2/Services_NYTimes
 */

namespace PEAR2\Services\NYTimes\Test;

/**
 * MainTest covers {@link \PEAR2\Services\NYTimes\Main}.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/Services_NYTimes
 */
class MainTest extends TestCase
{
    /**
     * @return array
     */
    public static function factoryProvider()
    {
        return array(
            array('newswire', 'PEAR2\Services\NYTimes\Newswire',),
            array('articlesearch', 'PEAR2\Services\NYTimes\Articlesearch',),
        );
    }

    /**
     * Test the factory.
     *
     * @return void
     *
     * @dataProvider factoryProvider
     */
    public function testFactory($api, $className)
    {
        $object = Main::factory($api, 'foo');
        $this->assertInstanceOf($className, $object);
    }

    /**
     * @expectedException \DomainException
     */
    public function testException()
    {
        Main::factory('WhatUpApi', 'foo');
    }
}
