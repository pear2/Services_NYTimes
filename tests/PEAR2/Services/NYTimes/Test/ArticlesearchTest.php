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

namespace PEAR2\Services\NYTimes\Test;

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
class ArticlesearchTest extends TestCase
{
    /**
     * @return void
     */
    public function testUrlSearch()
    {
        $responseObject = $this->setUpResponseObject(
            'articlesearch',
            'v1',
            'by-url.php'
        );

        $asMock = $this->getApiMocked('articlesearch', $responseObject);
        $data   = $asMock->byUrl('http://www.nytimes.com/2011/07/09/business/economy/job-growth-falters-badly-clouding-hope-for-recovery.html?hp');

        $this->assertInstanceOf('stdClass', $data);
    }

    public function testUrlSearchNoResult()
    {
        $responseObject = $this->setUpResponseObject(
            'articlesearch',
            'v1',
            'by-url-no-result.php'
        );

        $asMock = $this->getApiMocked('articlesearch', $responseObject);
        $data   = $asMock->byUrl('http://www.nytimes.com/2011/07/09/business/economy/job-growth-falters-badly-clouding-hope-for-recovery.html?hp');

        $this->assertFalse($data);
    }
}
