<?php

namespace unit;

use http\Client;
use PHPUnit\Framework\TestCase;
use seosazi\PathConverter\ConvertToAbsolutePath;

class ConvertToAbsolutePathTest extends TestCase
{

    /**
     * @dataProvider dataForTestUpToLastDir
     * @param $path
     * @param $result
     */
    public function testUpToLastDir($path, $result)
    {
        $address = new ConvertToAbsolutePath();
        $this->assertSame($address->upToLastDir($path), $result);
    }

    public function dataForTestUpToLastDir()
    {
        return [
            'page path with file name'  => [
                'http://example.com/some/fake/path/page.html',
                'http://example.com/some/fake/path/'
            ],
            'page path with any file name type 1' => [
                'http://example.com/some/fake/',
                'http://example.com/some/fake/'
            ],
            'page path with any file name type 2' => [
                'http://example.com/some/fake',
                'http://example.com/some/'
            ],
            'home page' => [
                'http://example.com',
                'http://example.com/'
            ]
        ];
    }

    public function testGetBaseTag()
    {

    }

    public function testSetPagePath()
    {

    }

    public function testConvert()
    {

    }

    public function testGetPagePath()
    {

    }

    public function testSetBaseTag()
    {

    }
}
