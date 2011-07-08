<?php
namespace PEAR2\Services\NYTimes;

class NewswireTest extends \PHPUnit_Framework_TestCase
{
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
    }

    /**
     * Lulz.
     *
     * @return void
     */
    public function testNewswireConstruct()
    {
        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', new Newswire('apikey'));
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
        $newswire = new Newswire(NEWSWIRE_API_KEY);
        $response = $newswire->getItemByUrl($url);

        $this->assertInstanceOf('stdClass', $response);
    }

    /**
     * Make sure XML wrapped in DOMDocument is returned.
     *
     * @return void
     */
    public function testGetItemByUrlInExEmHell()
    {
        $newswire = new Newswire(NEWSWIRE_API_KEY);
        $response = $newswire
            ->setResponseFormat('xml')
            ->getItemByUrl('http://www.nytimes.com/2011/07/09/science/space/09shuttle.html');

        $this->assertInstanceOf('\DOMDocument', $response);

        $this->assertEquals('OK', $response->getElementsByTagName('status')->item(0)->nodeValue);
    }
}
