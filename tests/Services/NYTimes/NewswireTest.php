<?php
namespace PEAR2\Services\NYTimes;

class NewswireTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!defined('NEWSWIRE_API_KEY')) {
            $this->markTestSkipped("This requires an API key.");
        }
    }

    public function testNewswireConstruct()
    {
        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', new Newswire('apikey'));
    }

    public static function urlProvider()
    {
        return array(
            array('http://www.nytimes.com/2011/07/09/science/space/09shuttle.html'),
            array('http://www.nytimes.com/2011/07/09/science/space/09shuttle.html?hp'),
        );
    }

    /**
     * @dataProvider urlProvider
     */
    public function testGetItemByUrl($url)
    {
        $newswire = new Newswire(NEWSWIRE_API_KEY);
        $response = $newswire->getItemByUrl($url);

        $this->assertInstanceOf('stdClass', $response);
    }
}
