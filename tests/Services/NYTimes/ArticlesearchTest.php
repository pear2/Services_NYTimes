<?php
/**
 * PEAR2\Services\NYTimes\ArticlesearchTest
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
 * ArticlesearchTest covers {@link \PEAR2\Services\NYTimes\Articlesearch}.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/Services_NYTimes
 */
namespace PEAR2\Services\NYTimes;
class ArticlesearchTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!defined('ARTICLESEARCH_API_KEY')) {
            $this->markTestSkipped("This requires an API key.");
        }
    }

    public function testUrlSearch()
    {
        $search = new Articlesearch(ARTICLESEARCH_API_KEY);
    }
}
