<?php
/**
 * PEAR2\Services\NYTimes\TestCase
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
 * An abstract TestCase class for test cases.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/Services_NYTimes
 */
namespace PEAR2\Services\NYTimes;
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Helper function to setup the object to be injected into the mock.
     *
     * @param string $apiName    The name of the API.
     * @param string $apiVersion The version of the API.
     * @param string $fixture    The name of the fixture file.
     *
     * @return \HTTP_Request2_Response
     */
    protected function setUpResponseObject($apiName, $apiVersion, $fixture)
    {
        $data = include __DIR__ . "/../../fixtures/{$apiName}/{$apiVersion}/{$fixture}";

        $response = new \HTTP_Request2_Response(
            $data['statusLine'],
            true,
            $data['effectiveUrl']
        );

        $response->appendBody($data['body']);
        return $response;
    }
}
