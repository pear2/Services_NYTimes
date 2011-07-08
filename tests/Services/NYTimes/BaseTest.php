<?php
namespace PEAR2\Services\NYTimes;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testFormat()
    {
        $newswire = new Newswire('apikey');
        $newswire->setResponseFormat('yaml'); // haha, yaml, haha Symfony
    }

    public function testSetRequest()
    {
        $newswire = new Newswire('apikey');

        $req = new \HTTP_Request2;
        $req->setAdapter('mock');

        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', $newswire->setRequestObject($req));
    }
}
