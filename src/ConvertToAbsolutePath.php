<?php


namespace seosazi\PathConverter;


/**
 * Convert paths relative with base tag and domain to absolute path
 *
 * E.g.
 *      href="style.css"
 *      and base tag "https://www.seosazi.com/assets/"
 *      and web page address "https://www.seosazi.com"
 * becomes
 *     https://www.seosazi.com/assets/style.css
 *
 * Or
 *E.g.
 *      href="../../style.css"
 *      and base tag "https://www.seosazi.com/assets/files/"
 *      and web page address "https://www.seosazi.com"
 * becomes
 *     https://www.seosazi.com/style.css
 *
 *
 * Please report bugs on https://github.com/seosazi/path-converter/issues
 *
 * @author Pouya Pormohamad <p.pormohamad@gmail.com>
 * @copyright Copyright (c) 2020, Pouya Pormohamad. All rights reserved
 * @license MIT License
 */
class ConvertToAbsolutePath implements ConverterInterface
{

    /** @var string */
    private $pagePath;
    /** @var string|null */
    private $baseTag = null;
    /** @var string|null  */
    private $starterPath=null;
    /**
     * @var array|false|int|string|null
     */
    private $baseTagParsing;
    /**
     * @var array|false|int|string|null
     */
    private $pagePathParsing;
    /**
     * @var string
     */
    private $domain;
    private $scheme;

    /**
     * ConvertToAbsolutePath constructor.
     * @param $pagePath
     */
    public function __construct($pagePath=NULL)
    {
        if(isset($pagePath)) {
            $this->setPagePath($pagePath);
        }
    }

    /**
     * @inheritDoc
     */
    public function convert($path)
    {
        // Skip converting if the relative url like http://... or android-app://... etc.
        if ($this->isPathAbsoluteOrForAnotherApp($path)) {
            return $this->checkPathIsAbsoluteOrForAnotherApp($path);
        }
        // Treat path as invalid if it is like javascript:... etc.
        if ($this->isPathJavaScript($path)) {
            return '';
        }
        // Convert //www.google.com to http://www.google.com
        if($this->isPathWithoutScheme($path)) {
            return 'http:' . $path;
        }
        // If the path is a fragment or query string,
        // it will be appended to the base url
        if($this->isHaveQueryOrFragment($path)) {
            return $this->getStarterPath() . $path;
        }
        // Treat paths with doc root, i.e, /about
        if($this->isPathStartWithSlash($path)) {
            return $this->onlySitePath($this->getStarterPath()) . $path;
        }
        // For paths like ./foo, it will be appended to the furthest directory
        if($this->isPathStartWithPointSlash($path)) {
            return $this->uptoLastDir($this->getStarterPath()) . substr($path, 2);
        }
        // Convert paths like ../foo or ../../bar
        if($this->isPathStartWithTwoPointSlash($path)) {
            $removeTwoPointSlash = new RemovePathWithPointPointSlash($this, $path);
            return $removeTwoPointSlash->compute();
        }
        if (empty($path)) {
            return $this->getPagePath();
        }
        // else
        return $this->uptoLastDir($this->getStarterPath()) . $path;
    }

    public function onlySitePath($url) {
//        $url = preg_replace('/(^https?:\/\/.+?\/)(.*)$/i', '$1', $url);
//        return rtrim($url, '/');
        $parseUrl = parse_url($url);
        if ($this->isCorrectUrl($parseUrl)) {
            return '';
        } elseif(isset($parseUrl['scheme'])){
            return $parseUrl['scheme'] . '://' . $parseUrl['host'];
        } else {
            return $parseUrl['host'];
        }
    }

    // Get the path with last directory
    // http://example.com/some/fake/path/page.html => http://example.com/some/fake/path/
    public function upToLastDir($url) {
        $parseUrl = parse_url($url);
        $path = '';
        if(isset($parseUrl['path'])) {
                    $path = preg_replace('/\/([^\/]+)$/i', '', $parseUrl['path']);
        }
        return rtrim($parseUrl['scheme'] . '://' . $parseUrl['host'] . $path, '/') . '/';
    }


    public function getPagePath()
    {
        return $this->pagePath;
    }

    /**
     * @param string $pagePath
     */
    public function setPagePath(string $pagePath): void
    {
        $this->pagePath = $pagePath;
    }


    public function getBaseTag()
    {
        return $this->baseTag;
    }

    /**
     * @param string $baseTag
     */
    public function setBaseTag(string $baseTag): void
    {
        $this->baseTag = $baseTag;
    }

    /**
     * @return string
     */
    public function getStarterPath(): string
    {
        if($this->starterPath===null){
            if($this->getBaseTag() === null) {
                $this->starterPath =$this->getPagePath();
            } elseif(array_key_exists('scheme', $this->getBaseTagParsing())){
                $this->starterPath = $this->getBaseTag() ;
            } else{
                $this->starterPath = $this->getPagePathParsing()['scheme'] . '://' . $this->getPagePathParsing()['host'] . $this->getBaseTag();
            }
        }
        return $this->starterPath;
    }

    public function getDomain()
    {
        if ($this->domain === null) {
            if($this->getBaseTag() === null) {
                $this->domain = $this->getPagePathParsing()['host'] . '/';
            }elseif(array_key_exists('scheme', $this->getBaseTagParsing())){
                $this->domain = $this->getBaseTagParsing()['host'] . '/';
            }else{
                $this->domain = $this->getPagePathParsing()['host'] . '/';
            }
        }
        return $this->domain;
    }

    public function getScheme()
    {
        if ($this->scheme === null) {
            if($this->getBaseTag() === null) {
                $this->scheme = $this->getPagePathParsing()['scheme'];
            } elseif(array_key_exists('scheme', $this->getBaseTagParsing())){
                $this->scheme = $this->getBaseTagParsing()['scheme'];
            } else{
                $this->scheme = $this->getPagePathParsing()['scheme'];
            }
        }
        return $this->scheme;
    }

    public function getBaseTagParsing()
    {
        if($this->baseTagParsing == null) {
            if(is_string($this->getBaseTag())) {
                $this->baseTagParsing = parse_url($this->getBaseTag());
            } else {
                $this->baseTagParsing = [];
            }
        }
        return $this->baseTagParsing;
    }

    public function getPagePathParsing()
    {
        if($this->pagePathParsing == null) {
            if(is_string($this->getPagePath())) {
                $this->pagePathParsing = parse_url($this->getPagePath());
            }else{
                $this->pagePathParsing = [];
            }
        }
        return $this->pagePathParsing;
    }

    /**
     * @param $path
     * @return bool
     */
    public function isHaveQueryOrFragment($path): bool
    {
        return substr($path, 0, 1) == '#' || substr($path, 0, 1) == '?';
    }

    /**
     * @param $parseUrl
     * @return bool
     */
    public function isCorrectUrl($parseUrl): bool
    {
        return !isset($parseUrl['scheme']) AND !isset($parseUrl['host']);
    }

    /**
     * @param $path
     * @return string
     */
    public function checkPathIsAbsoluteOrForAnotherApp($path): string
    {
        if (preg_match('/services:\/\//i', $path)) {
            return '';
        }
        if (preg_match('/whatsapp:\/\//i', $path)) {
            return '';
        }
        if (preg_match('/tel:/i', $path)) {
            return '';
        }
        return $path;
    }

    /**
     * @param $path
     * @return false|int
     */
    public function isPathAbsoluteOrForAnotherApp($path)
    {
        return preg_match('/[a-z0-9-]{1,}(:\/\/)/i', $path);
    }

    /**
     * @param $path
     * @return false|int
     */
    public function isPathJavaScript($path)
    {
        return preg_match('/^[a-zA-Z]{0,}:[^\/]{0,1}/i', $path);
    }

    /**
     * @param $path
     * @return bool
     */
    public function isPathWithoutScheme($path): bool
    {
        return substr($path, 0, 2) == '//';
    }

    /**
     * @param $path
     * @return bool
     */
    public function isPathStartWithSlash($path): bool
    {
        return substr($path, 0, 1) == '/';
    }

    /**
     * @param $path
     * @return bool
     */
    public function isPathStartWithPointSlash($path): bool
    {
        return substr($path, 0, 2) == './';
    }

    /**
     * @param $path
     * @return bool
     */
    public function isPathStartWithTwoPointSlash($path): bool
    {
        return substr($path, 0, 3) == '../';
    }
}