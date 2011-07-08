<?php
namespace PEAR2\Services\NYTimes;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testFormat()
    {
        $newswire = new Newswire;
        $newswire->setResponseFormat('yaml'); // haha, yaml, haha Symfony
    }
}
