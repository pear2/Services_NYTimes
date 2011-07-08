<?php
/**
 * PEAR2\Services\NYTimes\BaseTest
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

/**
 * BaseTest covers {@link \PEAR2\Services\NYTimes\Base}.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/Services_NYTimes
 */
namespace PEAR2\Services\NYTimes;
class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testFormat()
    {
        $newswire = new Newswire('apikey');
        $newswire->setResponseFormat('yaml'); // haha, yaml, haha Symfony
    }

    public function testSetRequest()
    {
        $newswire = new Newswire('apikey');

        $req = new \HTTP_Request2;
        $req->setAdapter('mock');

        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', $newswire->accept($req));
    }
}
