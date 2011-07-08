<?php
/**
 * PEAR2\Services\NYTimes\Main
 *
 * PHP version 5
 *
 * @category  Yourcategory
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id$
 * @link      http://svn.php.net/repository/pear2/PEAR2_Services_NYTimes
 */

/**
 * Main class for PEAR2_Services_NYTimes
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

    abstract protected function makeRequest($uri);
    abstract protected function parseResponse($response);
}
