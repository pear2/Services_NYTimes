<?php
/**
 * PEAR2\Services\NYTimes\NewswireTest
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
 * NewswireTest covers {@link \PEAR2\Services\NYTimes\Newswire}.
 *
 * This tests requires an api key to run until we mock it.
 *
 * @category  Services
 * @package   PEAR2_Services_NYTimes
 * @author    Till Klampaeckel <till@php.net>
 * @copyright 2011 Till Klampaeckel
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/Services_NYTimes
 */
namespace PEAR2\Services\NYTimes;
class NewswireTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PEAR2\Services\NYTimes\Newswire
     */
    protected $nw;

    /**
     * For now we skip all tests when no api key is defined.
     *
     * @return void
     */
    protected function setUp()
    {
        if (!defined('NEWSWIRE_API_KEY')) {
            $this->markTestSkipped("This requires an API key.");
        }
        $this->nw = new Newswire(NEWSWIRE_API_KEY);
    }

    /**
     * Lulz.
     *
     * @return void
     */
    public function testNewswireConstruct()
    {
        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', $this->nw);
    }

    /**
     * Created to make sure that query data is stripped.
     *
     * @return array
     * @see    self::testGetItemByUrl()
     */
    public static function urlProvider()
    {
        return array(
            array('http://www.nytimes.com/2011/07/09/science/space/09shuttle.html'),
            array('http://www.nytimes.com/2011/07/09/science/space/09shuttle.html?hp'),
        );
    }

    /**
     * Regular test item by url (json is used).
     *
     * @return void
     *
     * @dataProvider urlProvider
     */
    public function testGetItemByUrl($url)
    {
        $response = $this->nw->getItemByUrl($url);

        $this->assertInstanceOf('stdClass', $response);
    }

    /**
     * Make sure XML wrapped in DOMDocument is returned.
     *
     * @return void
     */
    public function testGetItemByUrlInExEmHell()
    {
        $response = $this->nw
            ->setResponseFormat('xml')
            ->getItemByUrl('http://www.nytimes.com/2011/07/09/science/space/09shuttle.html');

        $this->assertInstanceOf('\DOMDocument', $response);

        $this->assertEquals('OK', $response->getElementsByTagName('status')->item(0)->nodeValue);
    }

    /**
     * Confirm that we can do PHP too.
     *
     * @return void
     */
    public function testGetItemByUrlInPHP()
    {
        $response = $this->nw
            ->setResponseFormat('sphp')
            ->getItemByUrl('http://www.nytimes.com/2011/07/09/science/space/09shuttle.html');

        $this->assertInternalType('array', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('results', $response);
    }

    /**
     * Return all sections.
     *
     * @return void
     */
    public function testGetSections()
    {
        $sections = $this->nw->getSections();
        $this->assertInternalType('array', $sections);
    }
}
