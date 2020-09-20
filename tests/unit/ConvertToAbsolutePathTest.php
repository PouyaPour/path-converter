<?php

namespace unit;

use http\Client;
use PHPUnit\Framework\TestCase;
use seosazi\PathConverter\ConvertToAbsolutePath;

class ConvertToAbsolutePathTest extends TestCase
{
    /** @var ConvertToAbsolutePath */
    private $address;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->setAddress(
            new ConvertToAbsolutePath('http://example.com/some/fake/path/page.html')
        );
    }

//    public function testConstruct()
//    {
//        $this->expectException(new ConvertToAbsolutePath('/some/fake/path/page.html'));
//    }

    /**
     * @dataProvider dataForTestUpToLastDir
     * @param $path
     * @param $result
     */
    public function testUpToLastDir($path, $result)
    {
        $this->assertSame($this->getAddress()->upToLastDir($path), $result);
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

    /**
     * @dataProvider dataForOnlySitePath
     * @param $path
     * @param $result
     */
    public function testOnlySitePath($path, $result)
    {
        $this->assertSame($this->getAddress()->onlySitePath($path), $result);
    }

    public function dataForOnlySitePath()
    {
        return [
            'page path with file name'  => [
                'http://example.com/some/fake/path/page.html',
                'http://example.com'
            ],
            'page path with any file name type 1' => [
                'http://example.com/some/fake/',
                'http://example.com'
            ],
            'page path with any file name type 2' => [
                'http://example.com/some/fake',
                'http://example.com'
            ],
            'home page 1' => [
                'http://example.com',
                'http://example.com'
            ],
            'home page 2' => [
                'example.com/some/fake',
                ''
            ]
        ];
    }


    /**
     * @dataProvider dataForConvert
     * @param $pagePath
     * @param $baseTag
     * @param $path
     * @param $result
     */
    public function testConvert($pagePath, $baseTag, $path, $result)
    {
        if (isset($pagePath)) {
            $this->getAddress()->setPagePath($pagePath);
        }
        if (isset($baseTag)) {
            $this->getAddress()->setBaseTag($baseTag);
        }
        $this->assertSame($this->getAddress()->convert($path), $result);
    }

    public function dataForConvert()
    {
        return [
            'normal page with base 1'  => [
                'https://www.php.net/manual/en/function.parse-url.php',
                'https://www.php.net/manual/en/function.parse-url.php',
                '/images/notes-add@2x.png',
                'https://www.php.net/images/notes-add@2x.png'
            ],
            'normal page without base'  => [
                'https://kafshnice.ir/product-category/shoe/for-men/',
                null,
                'https://kafshnice.ir/wp-content/uploads/2020/01/2020-01-23_6-11-45-99x75.png',
                'https://kafshnice.ir/wp-content/uploads/2020/01/2020-01-23_6-11-45-99x75.png'
            ],
            'normal page without base 2'  => [
                'https://roadmap.sh/backend',
                null,
                '/_next/static/hxwb-QfpHEYaFAmytdA9D/pages/%5Broadmap%5D.js',
                'https://roadmap.sh/_next/static/hxwb-QfpHEYaFAmytdA9D/pages/%5Broadmap%5D.js'
            ],
            'normal page without base 3'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                null,
                '/bastam/resources/images/browserSupport/war_icon.png',
                'https://bastam.bankmellat.ir/bastam/resources/images/browserSupport/war_icon.png'
            ],
            'normal page with base 2'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                '/bastam/resources/images/',
                'browserSupport/war_icon.png',
                'https://bastam.bankmellat.ir/bastam/resources/images/browserSupport/war_icon.png'
            ],
            'normal page with base 3'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                'bastam/resources/images',
                'browserSupport/war_icon.png',
                'https://bastam.bankmellat.ir/bastam/resources/images/browserSupport/war_icon.png'
            ],
            'javascript' => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                null,
                'javascript:void(0)',
                ''
            ],
            'WithoutScheme' => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                null,
                '//bastam.bankmellat.ir/bastam/resources/images/browserSupport/war_icon.png',
                'http://bastam.bankmellat.ir/bastam/resources/images/browserSupport/war_icon.png'
            ],
            'QueryOrFragment' => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                null,
                '?ex=151.0_5',
                'https://bastam.bankmellat.ir/bastam/shahab_service?ex=151.0_5'
            ],
            'WithPointSlash' => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                null,
                './browserSupport/war_icon.png',
                'https://bastam.bankmellat.ir/bastam/browserSupport/war_icon.png'
            ],
            'with ../' => [
                'https://www.jquery-az.com/html/test/demo.php?ex=151.0_5',
                null,
                '../../banana.jpg',
                'https://www.jquery-az.com/banana.jpg'
            ],
            'empty path ' => [
                'https://www.jquery-az.com/html/test/demo.php',
                null,
                '',
                'https://www.jquery-az.com/html/test/demo.php',
            ]
        ];
    }

    /**
     * @dataProvider dataForGetDomain
     * @param $pagePath
     * @param $baseTag
     * @param $result
     */
    public function testGetDomain($pagePath, $baseTag, $result)
    {
        if (isset($pagePath)) {
            $this->getAddress()->setPagePath($pagePath);
        }
        if (isset($baseTag)) {
            $this->getAddress()->setBaseTag($baseTag);
        }
        $this->assertSame($this->getAddress()->getDomain(), $result);
        $this->assertSame($this->getAddress()->getDomain(), $result);
    }

    public function dataForGetDomain()
    {
        return [
            'normal page with base 1'  => [
                'https://www.php.net/manual/en/function.parse-url.php',
                'https://www.php.net/manual/en/function.parse-url.php',
                'www.php.net/'
            ],
            'normal page without base'  => [
                'https://kafshnice.ir/product-category/shoe/for-men/',
                null,
                'kafshnice.ir/'
            ],
            'normal page with base 2'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                '/bastam/resources/images/',
                'bastam.bankmellat.ir/'
            ]
        ];
    }

    /**
     * @dataProvider dataForGetScheme
     * @param $pagePath
     * @param $baseTag
     * @param $result
     */
    public function testGetScheme($pagePath, $baseTag, $result)
    {
        if (isset($pagePath)) {
            $this->getAddress()->setPagePath($pagePath);
        }
        if (isset($baseTag)) {
            $this->getAddress()->setBaseTag($baseTag);
        }
        $this->assertSame($this->getAddress()->getScheme(), $result);
        $this->assertSame($this->getAddress()->getScheme(), $result);
    }

    public function dataForGetScheme()
    {
        return [
            'normal page with base 1'  => [
                'https://www.php.net/manual/en/function.parse-url.php',
                'http://www.php.net/manual/en/function.parse-url.php',
                'http'
            ],
            'normal page without base'  => [
                'https://kafshnice.ir/product-category/shoe/for-men/',
                null,
                'https'
            ],
            'normal page with base 2'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                '/bastam/resources/images/',
                'https'
            ]
        ];
    }

    /**
     * @dataProvider dataForGetBaseTagParsing
     * @param $baseTag
     * @param $result
     */
    public function testGetBaseTagParsing($baseTag, $result)
    {
        if (isset($baseTag)) {
            $this->getAddress()->setBaseTag($baseTag);
        }
        $this->assertSame($this->getAddress()->getBaseTagParsing(), $result);
        $this->assertSame($this->getAddress()->getBaseTagParsing(), $result);
    }

    public function dataForGetBaseTagParsing()
    {
        return [
            'normal page with base tag null'  => [
                null,
                []
            ],
            'normal page with base tag'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                parse_url('https://bastam.bankmellat.ir/bastam/shahab_service')
            ]
        ];
    }

    /**
     * @dataProvider dataForGetPagePathParsing
     * @param $pagePath
     * @param $result
     */
    public function testGetPagePathParsing($pagePath, $result)
    {
        if (isset($pagePath)) {
            $this->getAddress()->setPagePath($pagePath);
        }
        $this->assertSame($this->getAddress()->getPagePathParsing(), $result);
        $this->assertSame($this->getAddress()->getPagePathParsing(), $result);
    }

    public function dataForGetPagePathParsing()
    {
        return [
            'normal page with base tag null'  => [
                null,
                parse_url('http://example.com/some/fake/path/page.html')
            ],
            'normal page with base tag'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                parse_url('https://bastam.bankmellat.ir/bastam/shahab_service')
            ]
        ];
    }

    /**
     * @dataProvider dataForCheckPathIsAbsoluteOrForAnotherApp
     * @param $path
     * @param $result
     */
    public function testCheckPathIsAbsoluteOrForAnotherApp($path, $result)
    {
        $this->assertSame($this->getAddress()->checkPathIsAbsoluteOrForAnotherApp($path), $result);
    }

    public function dataForCheckPathIsAbsoluteOrForAnotherApp()
    {
        return [
            'tel'  => [
                'tel:1-562-867-5309',
                ''
            ],
            'whatsapp'  => [
                'whatsapp://send?text=WHATEVER_LINK_OR_TEXT_YOU_WANT_TO_SEND',
                ''
            ],
            'services'  => [
                'services://send?text=WHATEVER_LINK_OR_TEXT_YOU_WANT_TO_SEND',
                ''
            ],
            'correct path'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                'https://bastam.bankmellat.ir/bastam/shahab_service'
            ]
        ];
    }

    /**
     * @dataProvider dataForIsPathStartWithPointSlash
     * @param $path
     * @param $result
     */
    public function testIsPathStartWithPointSlash($path, $result)
    {
        if (isset($pagePath)) {
            $this->getAddress()->setPagePath($pagePath);
        }
        $this->assertSame($this->getAddress()->isPathStartWithPointSlash($path), $result);
    }

    public function dataForIsPathStartWithPointSlash()
    {
        return [
            'true'  => [
                './bastam/shahab_service',
                true
            ],
            'false 1'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                false
            ],
            'false 2'  => [
                '/bastam/shahab_service',
                false
            ],
            'false 3'  => [
                '../bastam/shahab_service',
                false
            ]
        ];
    }

    /**
     * @dataProvider dataForIsPathStartWithTwoPointSlash
     * @param $path
     * @param $result
     */
    public function testIsPathStartWithTwoPointSlash($path, $result)
    {
        if (isset($pagePath)) {
            $this->getAddress()->setPagePath($pagePath);
        }
        $this->assertSame($this->getAddress()->isPathStartWithTwoPointSlash($path), $result);
    }

    public function dataForIsPathStartWithTwoPointSlash()
    {
        return [
            'true'  => [
                './bastam/shahab_service',
                false
            ],
            'false 1'  => [
                'https://bastam.bankmellat.ir/bastam/shahab_service',
                false
            ],
            'false 2'  => [
                '/bastam/shahab_service',
                false
            ],
            'false 3'  => [
                '../bastam/shahab_service',
                true
            ]
        ];
    }

    /**
     * @return ConvertToAbsolutePath
     */
    public function getAddress(): ConvertToAbsolutePath
    {
        return $this->address;
    }

    /**
     * @param ConvertToAbsolutePath $address
     */
    public function setAddress(ConvertToAbsolutePath $address): void
    {
        $this->address = $address;
    }
}
