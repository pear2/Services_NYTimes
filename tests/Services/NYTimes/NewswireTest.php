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
class NewswireTest extends TestCase
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
            array(
                'http://www.nytimes.com/2011/07/09/science/space/09shuttle.html',
                'shuttle-article.php',
                'v3',
            ),
            array(
                'http://www.nytimes.com/2011/07/09/science/space/09shuttle.html?hp',
                'shuttle-article.php',
                'v3',
            ),
        );
    }

    /**
     * Regular test item by url (json is used).
     *
     * @param string $url        The URL of the article.
     * @param string $fixture    The fixture file.
     * @param string $apiVersion The Newswire API version, e.g. v3.
     *
     * @return void
     *
     * @dataProvider urlProvider
     */
    public function testGetItemByUrl($url, $fixture, $apiVersion)
    {
        $responseObject = $this->setUpResponseObject(
            'newswire',
            $apiVersion,
            $fixture
        );

        $nwMock = $this->getApiMocked('newswire', $responseObject);

        $response = $nwMock->getItemByUrl($url);

        $this->assertInstanceOf('stdClass', $response);
        $this->assertObjectHasAttribute('status', $response);
        $this->assertObjectHasAttribute('copyright', $response);
        $this->assertObjectHasAttribute('num_results', $response);
        $this->assertObjectHasAttribute('results', $response);
    }

    /**
     * Make sure XML wrapped in DOMDocument is returned.
     *
     * @return void
     */
    public function testGetItemByUrlInExEmHell()
    {
        $responseObject = $this->setUpResponseObject(
            'newswire',
            'v3',
            'shuttle-article-xml.php'
        );

        $nwMock = $this->getApiMocked('newswire', $responseObject);

        $response = $nwMock
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
        $responseObject = $this->setUpResponseObject(
            'newswire',
            'v3',
            'shuttle-article-sphp.php'
        );

        $nwMock = $this->getApiMocked('newswire', $responseObject);

        $response = $nwMock
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

    /**
     *
     * @return void
     */
    public function testGetItems()
    {
        $response = $this->nw->getItems();
        $this->assertInternalType('array', $response);
    }

    public static function paramProvider()
    {
        return array(
            array('source', 'nyt',),
            array('section', 'all',),
            array('limit', 10,),
            array('offset', 5,),
            array('period', 24),
            array('period', 0),
        );
    }

    /**
     * @return void
     *
     * @dataProvider paramProvider
     */
    public function testMagic($param, $value)
    {
        $methodSet = 'set' . ucfirst($param);
        $methodGet = 'get' . ucfirst($param);

        $this->assertInstanceOf('\PEAR2\Services\NYTimes\Newswire', $this->nw->$methodSet($value));
        $this->assertEquals($value, $this->nw->$methodGet());
    }

    /**
     * Throw an exception for bogus source.
     *
     * @return void
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSetSource()
    {
        $this->nw->setSource('Interweb');
    }

    public static function exceptionProvider()
    {
        return array(
            array('setPeriod', 1000, "\\RangeException",), // 0–720 supported
            array('setLimit', 100, "\\RangeException",),
            array('setLimit', 'a', "\\InvalidArgumentException",),
            array('setOffset', 'b', "\\InvalidArgumentException",),
        );
    }

    /**
     * Test exceptions from __call().
     *
     * @return void
     *
     * @dataProvider exceptionProvider
     */
    public function testSetExceptions($method, $value, $exception)
    {
        $this->setExpectedException($exception);
        $this->nw->$method($value);
    }
}
