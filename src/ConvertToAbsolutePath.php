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
    /** @var string */
    private $baseTag;
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
     * @inheritDoc
     */
    public function convert($path)
    {
        // Skip converting if the relative url like http://... or android-app://... etc.
        if (preg_match('/[a-z0-9-]{1,}(:\/\/)/i', $path)) {
            if(preg_match('/services:\/\//i', $path))
                return null;
            if(preg_match('/whatsapp:\/\//i', $path))
                return null;
            if(preg_match('/tel:/i', $path))
                return null;
            return $path;
        }
        // Treat path as invalid if it is like javascript:... etc.
        if (preg_match('/^[a-zA-Z]{0,}:[^\/]{0,1}/i', $path)) {
            return NULL;
        }
        // Convert //www.google.com to http://www.google.com
        if(substr($path, 0, 2) == '//') {
            return 'http:' . $path;
        }
        // If the path is a fragment or query string,
        // it will be appended to the base url
        if(substr($path, 0, 1) == '#' || substr($path, 0, 1) == '?') {
            return $this->getStarterPath() . $path;
        }
        // Treat paths with doc root, i.e, /about
        if(substr($path, 0, 1) == '/') {
            return static::onlySitePath($this->getStarterPath()) . $path;
        }
        // For paths like ./foo, it will be appended to the furthest directory
        if(substr($path, 0, 2) == './') {
            return static::uptoLastDir($this->getStarterPath()) . substr($path, 2);
        }
        // Convert paths like ../foo or ../../bar
        if(substr($path, 0, 3) == '../') {
            $rel = $path;
            $base = static::uptoLastDir($this->getStarterPath());
            while(substr($rel, 0, 3) == '../') {
                $base = preg_replace('/\/([^\/]+\/)$/i', '/', $base);
                $rel = substr($rel, 3);
            }
            if ($base === ($this->getScheme() . '://')) {
                $base .= $this->getDomain();
            } elseif ($base===($this->getScheme(). ':/')) {
                $base .= '/' . $this->getDomain();
            }
            return $base . $rel;
        }
        if (empty($path)) {
            return $this->getPagePath();
        }
        // else
        return static::uptoLastDir($this->getStarterPath()) . $path;
    }

    private function onlySitePath($url) {
        $url = preg_replace('/(^https?:\/\/.+?\/)(.*)$/i', '$1', $url);
        return rtrim($url, '/');
    }

    // Get the path with last directory
    // http://example.com/some/fake/path/page.html => http://example.com/some/fake/path/
    public function upToLastDir($url) {
//        $url = preg_replace('/\/([^\/]+\.[^\/]+)$/i', '', $url);
        $url = preg_replace('/\/([^\/]+)$/i', '', $url);
        return rtrim($url, '/') . '/';
    }

    /**
     * @return string
     */
    public function getPagePath(): string
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

    /**
     * @return string
     */
    public function getBaseTag(): string
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
    private function getStarterPath(): string
    {
        if($this->starterPath===null){
            if($this->getBaseTag() === null) {
                $this->starterPath =$this->getPagePath();
            }elseif(array_key_exists('scheme', $this->getBaseTagParsing())){
                $this->starterPath = $this->getBaseTag() ;
            }else{
                $this->starterPath = $this->getPagePathParsing()['scheme'] . '://' . $this->getPagePathParsing()['host'] . $this->getBaseTag();
            }
        }
        return $this->starterPath;
    }

    private function getDomain()
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

    private function getScheme()
    {
        if ($this->scheme === null) {
            if($this->getBaseTag() === null) {
                $this->scheme = $this->getPagePathParsing()['scheme'];
            }elseif(array_key_exists('scheme', $this->getBaseTagParsing())){
                $this->scheme = $this->getBaseTagParsing()['scheme'];
            }else{
                $this->scheme = $this->getPagePathParsing()['scheme'];
            }
        }
        return $this->scheme;
    }

    private function getBaseTagParsing()
    {
        if($this->baseTagParsing == null)
            $this->baseTagParsing = parse_url($this->getBaseTag());
        return $this->baseTagParsing;
    }

    private function getPagePathParsing()
    {
        if($this->pagePathParsing == null)
            $this->pagePathParsing = parse_url($this->getPagePath());
        return $this->pagePathParsing;
    }
}