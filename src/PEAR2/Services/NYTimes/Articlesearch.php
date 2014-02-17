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

use HTTP_Request2_Response;
use stdClass;

/**
 * A class interface for the NYTimes Article Search API.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/Services_NYTimes
 * @link      http://developer.nytimes.com/docs/read/article_search_api
 * @link      http://developer.nytimes.com/attribution
 */
class Articlesearch extends Base implements NYTimesInterface
{
    /**
     * @var string $apiVersion The NYTimes' API version.
     */
    protected $apiVersion = 'v1';

    /**
     * @var string $baseUri The base URI for all requests against the
     *     Article Search API.
     * @see self::getUri()
     */
    protected $baseUri = 'http://api.nytimes.com/svc/search/v2/articlesearch.json';

    /**
     * Search by URL.
     *
     * Returns a \stdClass if successful, or false when not (no result).
     *
     * @param string $url A URL to an article.
     *
     * @return stdClass|false
     */
    public function byUrl($url)
    {
        $uri = $this->getUri(array(
            'fq' => sprintf('web_url:"%s"', $this->cleanUrl($url)
        )));

        $response = $this->makeRequest($uri);

        $data = $this->parseResponse($response);
        if (empty($data->response->docs)) {
            return false;
        }

        return $data->response->docs[0];
    }

    /**
     * Build the endpoint.
     *
     * @param array $params
     *
     * @return string
     */
    protected function getUri(array $params = null)
    {
        $endpoint = $this->baseUri;
        if ($params === null) {
            $params = array();
        }
        $params['api-key'] = $this->key;

        $endpoint .= '?' . http_build_query($params);
        return $endpoint;
    }

    /**
     * Parses a response
     * 
     * @param HTTP_Request2_Response $response The response to parse
     * 
     * @return mixed The parsed JSON body.
     */
    protected function parseResponse(HTTP_Request2_Response $response)
    {
        if (!$this->isSuccessful($response)) {
            $this->hazProblem($response);
        }
        return json_decode($response->getBody());
    }
}
