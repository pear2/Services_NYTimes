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
namespace PEAR2\Services\NYTimes;
class Articlesearch extends Base implements NYTimesInterface
{
    /**
     * @var string $apiVersion The NYTimes' API version.
     */
    protected $apiVersion = 'v1';

    /**
     * @var string $baseUri The base URI for all requests against the Article Search API.
     * @see self::getUri()
     */
    protected $baseUri = 'http://api.nytimes.com/svc/search/v1/article';

    /**
     * Search by URL.
     *
     * @param string $url A URL to an article.
     *
     * @return stdClass
     */
    public function byUrl($url)
    {
        $uri = $this->getUri(array(
            'query' => 'url:' . $this->cleanUrl($url))
        );

        $response = $this->makeRequest($uri);

        $data = $this->parseResponse($response);

        return $data->results[0];
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

    protected function parseResponse(\HTTP_Request2_Response $response)
    {
        if (!$this->isSuccessful($response)) {
            $this->hazProblem($response);
        }
        return json_decode($response->getBody());
    }
}
