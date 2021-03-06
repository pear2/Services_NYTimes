<?php
/**
 * PEAR2\Services\NYTimes\Newswire
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
use DOMDocument;
use Exception;
use HTTP_Request2_Response;
use InvalidArgumentException;
use LogicException;
use RangeException;
use RuntimeException;

/**
 * A class interface for the NYTimes Newswire API.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/Services_NYTimes
 * @link      http://developer.nytimes.com/docs/times_newswire_api/
 * @link      http://developer.nytimes.com/attribution
 */
class Newswire extends Base implements NYTimesInterface
{
    /**
     * @var string $apiVersion The NYTimes' API version. There's also v2.
     */
    protected $apiVersion = 'v3';

    /**
     * @var string $baseUri The base URI for all requests against the Newswire API.
     * @see self::getUri()
     */
    protected $baseUri = 'http://api.nytimes.com/svc/news/v3/content';

    protected $searchParams = array(
        'source'  => 'all',
        'section' => 'all',
        'limit'   => 20,
        'offset'  => 0,
        'period'  => 0,
    );

    /**
     * This wraps around {@link self::$searchParams}
     *
     * @param string $method Must start with 'set' or 'get'
     * @param mixed  $args   The method arguments.
     *
     * @return mixed $this from a set*(), the value from get*().
     * @throws LogicException           In case of another method not trapped.
     * @throws InvalidArgumentException
     * @throws RangeException
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($args[0]) || (empty($args[0]) && $args[0] !== 0)) {
                throw new InvalidArgumentException(
                    "Cannot set an empty parameter."
                );
            }
            $param = $this->getParam($method);
            $value = $args[0];

            switch ($param) {
            case 'limit':
            case 'offset':
                if (!is_int($value)) {
                    throw new InvalidArgumentException(
                        "{ucfirst($param)} must be an integer."
                    );
                }
                if ($param == 'limit' && $value > 20) {
                    throw new RangeException("Limit cannot be greater than 20");
                }
                break;
            case 'period':
                if (!is_int($value)) {
                    throw new InvalidArgumentException(
                        "Period must be an integer between 0 and 720"
                    );
                }
                if ($value > 720) {
                    throw new RangeException(
                        "Period cannot be greater than 720"
                    );
                }
                break;
            case 'source':
                static $supportedSources = array('all', 'nyt', 'iht');
                if (!in_array($value, $supportedSources)) {
                    throw new InvalidArgumentException(
                        "Unsupported source: {$value}"
                    );
                }
                break;
            }
            $this->searchParams[$param] = $value;
            return $this;
        }
        if (substr($method, 0, 3) == 'get') {
            $param = $this->getParam($method);
            return $this->searchParams[$param];
        }
        throw new LogicException("Problem?");
    }

    /**
     * Return meta data about an article by URL.
     *
     * Only 'recent' articles will work.
     *
     * @param string $url The URL of the article
     *
     * @return mixed
     */
    public function getItemByUrl($url)
    {
        $url = $this->cleanUrl($url);

        $uri = $this->getUri(array('url' => $url));

        $response = $this->makeRequest($uri);
        return $this->parseResponse($response);
    }

    /**
     * Get items: <http://api.nytimes.com/svc/news/{version}/content/
     * {source}/{section}[/time-period][.response-format]?api-key=...>
     *
     * @return array
     */
    public function getItems()
    {
        $currentFormat = $this->format;

        $endpoint  = $this->getSource();
        $endpoint .= '/' . $this->getSection();

        $period = $this->getPeriod();
        if ($period > 0) {
            $endpoint .= '/' . $period;
        }

        $params           = array();
        $params['limit']  = $this->getLimit();
        $params['offset'] = $this->getOffset();

        $uri = $this->setResponseFormat('sphp')->getUri($params, $endpoint);

        $response = $this->makeRequest($uri);
        $data     = $this->parseResponse($response);

        $this->setResponseFormat($currentFormat);

        return $data['results'];
    }

    /**
     * Get all available sections.
     *
     * Protip: cache this so you don't burn through your API calls.
     *
     * @return array
     */
    public function getSections()
    {
        $currentFormat = $this->format;

        // makes it easier for us
        $this->setResponseFormat('sphp');

        $uri      = $this->getUri(null, 'section-list');
        $response = $this->makeRequest($uri);
        $data     = $this->parseResponse($response);

        if ($data['num_results'] == 0 || $data['status'] != 'OK') {
            throw new RuntimeException("Error: currently no sections are returned.");
        }

        $this->setResponseFormat($currentFormat);

        $sections = array();
        foreach ($data['results'] as $section => $displayName) {
            if (empty($section)) {
                continue;
            }
            $sections[$section] = $displayName;
        }
        return $sections;
    }

    /**
     * Get (and validate) the param from the method name.
     *
     * @param string $method The method from {@link self::__call()}
     *
     * @return string
     * @see    self::__call()
     * @throws DomainException When someone tries to set an unknown parameter.
     */
    protected function getParam($method)
    {
        $param = strtolower(substr($method, 3));
        if (!isset($this->searchParams[$param])) {
            throw new DomainException(
                "Unknown parameter: {$param} (from {$method})"
            );
        }
        return $param;
    }

    /**
     * Build the uri used to make a request.
     *
     * @param array  $params   Optional: For the query string.
     * @param string $endpoint Optional: In case we need to add to
     *                         {@link self::$baseUri}.
     *
     * @return string
     * @see    self::makeRequest()
     */
    protected function getUri(array $params = null, $endpoint = null)
    {
        if ($params === null) {
            $params = array();
        }
        $params['api-key'] = $this->key;

        $uri = $this->baseUri;
        if ($endpoint !== null) {
            $uri .= '/' . $endpoint;
        }

        return $uri
            . ".{$this->format}"
            . '?' . http_build_query($params);
    }

    /**
     * Parses a response
     * 
     * @param HTTP_Request2_Response $response The response to parse
     *
     * @return mixed
     * @uses   parent::isSuccessful()
     * @uses   parent::hazProblem()
     */
    protected function parseResponse(HTTP_Request2_Response $response)
    {
        if (!$this->isSuccessful($response)) {
            $this->hazProblem($response);
        }
        $body = $response->getBody();
        if ($this->format == 'json') {
            $data = json_decode($body);
        } elseif ($this->format == 'xml') {
            $data = new DOMDocument();
            $data->loadXML($body);
        } elseif ($this->format == 'sphp') {
            $data = unserialize($body);
        } else {
            throw new Exception("Not implemented.");
        }
        return $data;
    }
}
