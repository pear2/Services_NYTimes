<?php
namespace PEAR2\Services\NYTimes;

class MainTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $newswire = Main::factory('newswire', API_KEY);
        $this->assertInstanceOf('PEAR2\Services\NYTimes\Newswire', $newswire);
    }

    /**
     * @expectedException \DomainException
     */
    public function testException()
    {
        Main::factory('WhatUpApi', 'foo');
    }
}
