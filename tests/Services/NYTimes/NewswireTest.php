<?php
namespace PEAR2\Services\NYTimes;

class NewswireTest extends \PHPUnit_Framework_TestCase
{
    public function testNewswireConstruct()
    {
        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', new Newswire);
    }

    public function testSetUrl()
    {
        $newswire = new Newswire;
        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', $newswire->setUrl('http://example.org'));
    }
}
