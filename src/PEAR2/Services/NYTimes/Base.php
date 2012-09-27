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
     * Strip query from url.
     *
     * @param string $url
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
        switch ($response->getStatus()) {
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

    /**
     * Make a request! Woo!!!
     *
     * @param string $uri
     *
     * @return \HTTP_Request2_Response
     * @throws \RuntimeException When the transport fails.
     */
    protected function makeRequest($uri)
    {
        try {
            if (!($this->req instanceof \HTTP_Request2)) {
                $this->req = new \HTTP_Request2;
            }
            return $this->req->setUrl($uri)->send();
        } catch (\HTTP_Request2_Exception $e) {
            // push into a \RuntimeException, this is not very elegant
            $e = (string) $e;
            throw new \RuntimeException($e);
        }
    }

    abstract protected function getUri();
    abstract protected function parseResponse(\HTTP_Request2_Response $response);
}
