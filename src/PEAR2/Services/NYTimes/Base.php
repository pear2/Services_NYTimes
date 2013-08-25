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
 * @link      https://github.com/pear2/Services_NYTimes
 */
namespace PEAR2\Services\NYTimes;

use DomainException;
use HTTP_Request2;
use HTTP_Request2_Exception;
use HTTP_Request2_Response;
use InvalidArgumentException;
use LogicException;
use RuntimeException;

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
 * @link      https://github.com/pear2/Services_NYTimes
 * @link      http://developer.nytimes.com/attribution
 */
abstract class Base
{
    protected $format = 'json';

    /**
     * @var string $key The API key.
     * @see http://developer.nytimes.com/apps/register
     */
    protected $key;

    /**
     * @var HTTP_Request2 $req
     */
    protected $req;

    /**
     * __construct()
     *
     * @param string $key The API key.
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
        if ($mixed instanceof HTTP_Request2) {
            $this->req = $mixed;
            return $this;
        }
        throw new DomainException("Problem?");
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
     * @param string $format One of "json", "xml" or "sphp".
     *
     * @return $this
     * @throws InvalidArgumentException
     * @uses   self::$format
     * @see    parent::makeRequest()
     */
    public function setResponseFormat($format)
    {
        static $supported = array('json', 'xml', 'sphp');
        if (!in_array($format, $supported)) {
            throw new InvalidArgumentException("Format {$format} is not supported.");
        }
        $this->format = $format;
        return $this;
    }

    /**
     * Strip query from url.
     *
     * @param string $url The URL to clean.
     *
     * @return string
     */
    protected function cleanUrl($url)
    {
        $parts = parse_url($url);

        return $parts['scheme']
            . '://' . $parts['host']
            . $parts['path'];
    }

    /**
     * Return the HTTP_Request2 object, or null.
     *
     * @return mixed HTTP_Request2|null
     */
    public function getRequestObject()
    {
        return $this->req;
    }

    /**
     * Determine if the response is valid.
     *
     * @param HTTP_Request2_Response $response The response to check.
     *
     * @return boolean
     */
    protected function isSuccessful(HTTP_Request2_Response $response)
    {
        if ($response->getStatus() == 200) {
            return true;
        }
        return false;
    }

    /**
     * Determine the problem.
     * 
     * @param HTTP_Request2_Response $response The response to check.
     *
     * @return void
     */
    protected function hazProblem(HTTP_Request2_Response $response)
    {
        switch ($response->getStatus()) {
        case 400:
            throw new RuntimeException("Bad request.");
        case 403:
            throw new RuntimeException("You seem to be rate-limited.");
        case 404:
            throw new LogicException("Resource does not exist.");
        case 500:
            throw new RuntimeException("Please try again later.");
        default:
            throw new DomainException("An error occurred: {$response->getStatus()}");
        }
    }

    /**
     * Make a request! Woo!!!
     *
     * @param string $uri The URI to make a request to.
     *
     * @return HTTP_Request2_Response The response.
     * @throws RuntimeException When the transport fails.
     */
    protected function makeRequest($uri)
    {
        try {
            if (!($this->req instanceof HTTP_Request2)) {
                $this->req = new HTTP_Request2;
            }
            return $this->req->setUrl($uri)->send();
        } catch (HTTP_Request2_Exception $e) {
            // push into a \RuntimeException, this is not very elegant
            $e = (string) $e;
            throw new RuntimeException($e);
        }
    }

    /**
     * Build the endpoint.
     * 
     * @return string
     */
    abstract protected function getUri();

    /**
     * Parses a response
     * 
     * @param HTTP_Request2_Response $response The response to parse
     * 
     * @return mixed The parsed JSON body.
     */
    abstract protected function parseResponse(HTTP_Request2_Response $response);
}
