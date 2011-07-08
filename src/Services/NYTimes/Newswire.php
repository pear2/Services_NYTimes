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
 * @link      http://svn.php.net/repository/pear2/PEAR2_Services_NYTimes
 */

/**
 * A class interface for the NYTimes Newswire API.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://svn.php.net/repository/pear2/PEAR2_Services_NYTimes
 */
namespace PEAR2\Services\NYTimes;
class Newswire extends Base implements NYTimesInterface
{
    /**
     * @var string $apiVersion The NYTimes' API version. There's also v2.
     */
    protected $apiVersion = 'v3';

    protected $baseUri = 'http://api.nytimes.com/svc/news/v3/content';

    public function getItemByUrl($url)
    {
        $url = $this->cleanUrl($url);

        $uri = $this->getUri(array('url' => $url));

        $response = $this->makeRequest($uri);
        return $this->parseResponse($response);
    }

    protected function getUri(array $params = null)
    {
        if ($params === null) {
            $params = array();
        }
        $params['api-key'] = $this->key;

        return $this->baseUri
            . ".{$this->format}"
            . '?' . http_build_query($params);
    }

    /**
     * Make a request! Woo!!!
     *
     * @param string $uri
     *
     * @return \HTTP_Request2_Response
     */
    protected function makeRequest($uri)
    {
        if (!($this->req instanceof \HTTP_Request2)) {
            $this->req = new \HTTP_Request2;
        }
        return $this->req->setUrl($uri)->send();
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
