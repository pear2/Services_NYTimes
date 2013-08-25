<?php
/**
 * PEAR2\Services\NYTimes\Main
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

/**
 * Main class for PEAR2_Services_NYTimes
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/Services_NYTimes
 * @link      http://developer.nytimes.com/attribution
 */
class Main
{
    /**
     * A class factory.
     *
     * @param string $api The name of the API to use.
     * @param string $key The API Key.
     *
     * @return \PEAR2\Services\NYTimes\Base
     */
    public static function factory($api, $key)
    {
        static $supported = array('Newswire', 'Articlesearch',);
        $api = ucfirst(strtolower($api));

        if (!in_array($api, $supported)) {
            throw new \DomainException("Currently not supported: {$api}");
        }

        $className = "PEAR2\Services\NYTimes\\" . $api;

        return new $className($key);
    }
}
