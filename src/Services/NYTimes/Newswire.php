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

/**
 * A class interface for the NYTimes Newswire API.
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
            throw new \RuntimeException("Error: currently no sections are returned.");
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

    /**
     * @param \HTTP_Request2_Response $response
     *
     * @return mixed
     * @uses   parent::isSuccessful()
     * @uses   parent::hazProblem()
     */
    protected function parseResponse(\HTTP_Request2_Response $response)
    {
        if (!$this->isSuccessful($response)) {
            $this->hazProblem($response);
        }
        $body = $response->getBody();
        if ($this->format == 'json') {
            $data = json_decode($body);
        } elseif ($this->format == 'xml') {
            $data = new \DOMDocument();
            $data->loadXML($body);
        } elseif ($this->format == 'sphp') {
            $data = unserialize($body);
        } else {
            throw new \Exception("Not implemented.");
        }
        return $data;
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
}
