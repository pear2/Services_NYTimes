<?php
/**
 * PEAR2\Services\NYTimes\Base
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
 * Base class for PEAR2_Services_NYTimes
 *
 * This is implemented by each API.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://svn.php.net/repository/pear2/PEAR2_Services_NYTimes
 */
namespace PEAR2\Services\NYTimes;
abstract class Base
{
    protected $format = 'json';

    /**
     * @var string $key The API key.
     * @see http://developer.nytimes.com/apps/register
     */
    protected $key;

    /**
     * @var \HTTP_Request2 $req
     */
    protected $req;

    /**
     * __construct()
     *
     * @param string $key The API key.
     *
     * @return $this
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Acceptor pattern.
     *
     * @param mixed $mixed
     *
     * @return $this
     */
    public function accept($mixed)
    {
        if ($mixed instanceof \HTTP_Request2) {
            $this->req = $mixed;
            return $this;
        }
        throw new \DomainException("Problem?");
    }

    /**
     * This set of classes will support a distinct version of each API. This method
     * allows the developer to return the API's version programmatically.
     *
     * @return string
     * @see    parent::$apiVersion
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Set another response format.
     *
     * @param string $format
     *
     * @return $this
     * @throws \InvalidArgumentException
     * @uses   self::$format
     * @see    parent::makeRequest()
     */
    public function setResponseFormat($format)
    {
        static $supported = array('json', 'xml', 'sphp');
        if (!in_array($format, $supported)) {
            throw new \InvalidArgumentException("Format {$format} is not supported.");
        }
        $this->format = $format;
        return $this;
    }

    /**
     * Determine if the response is valid.
     *
     * @param \HTTP_Request2_Response $response
     *
     * @return boolean
     */
    protected function isSuccessful(\HTTP_Request2_Response $response)
    {
        if ($response->getStatus() == 200) {
            return true;
        }
        return false;
    }

    /**
     * Determine the problem.
     *
     * @return void
     */
    protected function hazProblem(\HTTP_Request2_Response $response)
    {
        switch ($response->getStatus) {
        case 400:
            throw new \RuntimeException("Bad request.");
        case 403:
            throw new \RuntimeException("You seem to be rate-limited.");
        case 404:
            throw new \LogicException("Resource does not exist.");
        case 500:
            throw new \RuntimeException("Please try again later.");
        default:
            throw new \DomainException("An error occurred: {$response->getStatus()}");
        }
    }

    abstract protected function getUri();
    abstract protected function makeRequest($uri);
    abstract protected function parseResponse(\HTTP_Request2_Response $response);
}
