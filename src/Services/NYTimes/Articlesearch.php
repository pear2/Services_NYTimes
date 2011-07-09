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
 * @link      http://developer.nytimes.com/docs/times_newswire_api/
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

    protected function getUri()
    {
        return $this->baseUri;
    }

    protected function makeRequest($uri)
    {

    }

    protected function parseResponse(\HTTP_Request2_Response $response)
    {

    }
}
