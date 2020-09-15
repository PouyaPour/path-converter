<?php


namespace seosazi\PathConverter;


class RemovePathWithPointPointSlash
{
    /** @var string */
    private $path;
    /** @var string */
    private $starterPath;
    /** @var string */
    private $scheme;
    /** @var string */
    private $domain;
    public function __construct(ConvertToAbsolutePath $convertToAbsolutePath, string $path)
    {
        $this->setPath($path);
        $this->setStarterPath($convertToAbsolutePath->uptoLastDir($convertToAbsolutePath->getStarterPath()));
        $this->setScheme($convertToAbsolutePath->getScheme());
        $this->setDomain($convertToAbsolutePath->getDomain());
    }

    public function compute()
    {
        while(substr($this->getPath(), 0, 3) == '../') {
            $this->setStarterPath(preg_replace('/\/([^\/]+\/)$/i', '/', $this->getStarterPath()));
            $this->setPath(substr($this->getPath(), 3));
        }
        if ($this->getStarterPath() === ($this->getScheme() . '://')) {
            $this->setStarterPath($this->getStarterPath() . $this->getDomain());
        } elseif ($this->getStarterPath() ===($this->getScheme(). ':/')) {
            $this->setStarterPath($this->getStarterPath() . '/' . $this->getDomain());
        }
        return $this->getStarterPath() . $this->getPath();
    }



    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getStarterPath(): string
    {
        return $this->starterPath;
    }

    /**
     * @param string $starterPath
     */
    public function setStarterPath(string $starterPath): void
    {
        $this->starterPath = $starterPath;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     */
    public function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }
}