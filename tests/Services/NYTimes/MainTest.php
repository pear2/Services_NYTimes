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
 * @link      http://svn.php.net/repository/pear2/PEAR2_Services_NYTimes
 */

/**
 * MainTest covers {@link \PEAR2\Services\NYTimes\Main}.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://svn.php.net/repository/pear2/PEAR2_Services_NYTimes
 */
namespace PEAR2\Services\NYTimes;
class MainTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $newswire = Main::factory('newswire', 'foo');
        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', $newswire);
    }

    /**
     * @expectedException \DomainException
     */
    public function testException()
    {
        Main::factory('WhatUpApi', 'foo');
    }
}
