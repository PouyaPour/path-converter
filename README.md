# path-converter

[![Build Status](https://travis-ci.com/seosazi/path-converter.svg?branch=master)](https://travis-ci.com/seosazi/path-converter)
[![Code coverage](http://img.shields.io/codecov/c/github/seosazi/path-converter.svg)](https://codecov.io/github/seosazi/path-converter)
[![Code quality](http://img.shields.io/scrutinizer/g/seosazi/path-converter.svg)](https://scrutinizer-ci.com/g/seosazi/path-converter)
[![Latest version](http://img.shields.io/packagist/v/seosazi/path-converter.svg)](https://packagist.org/packages/seosazi/path-converter)
[![License](http://img.shields.io/packagist/l/seosazi/path-converter.svg)](https://github.com/matthiasmullie/path-converter/blob/master/LICENSE)

Path converter is tools to convert relative path with base tag or not to absolute path. In different situation this class tested and must be works correct, If you find some issue, please tell me.

## Installing Path Converter

This package can be found on packagist and is best loaded using composer. We support php 5.0, 7.0.
The recommended way to install Path-Converter is through [Composer](https://getcomposer.org/).

**composer.phar**
```
 "require": {
    "seosazi/path-converter": "^1.0"
}
```
or
```
 composer require seosazi/path-converter
``` 

## Usage

First you create an object of this class and set the page address as a parameter. Second, set the base tag address, if any. Finally, you can use Convert to change any address on this page to absolute path.
 
```php
<?php

use seosazi\PathConverter\ConvertToAbsolutePath;
require_once "vendor/autoload.php";

$convert = new ConvertToAbsolutePath('https://www.jquery-az.com/html/test/demo.php?ex=151.0_5');
$path1 = $convert->convert('../../banana.jpg'); // https://www.jquery-az.com/banana.jpg
$path2 = $convert->convert('./bastam/shahab_service'); // https://www.jquery-az.com/html/test/bastam/shahab_service
$path3 = $convert->convert('browserSupport/war_icon.png'); // https://www.jquery-az.com/html/test/browserSupport/war_icon.png

 //and another example with base base tag
$convert = new ConvertToAbsolutePath('https://www.example.com/bastam/shahab_service');
$convert->setBaseTag('https://bastam.bankmellat.ir/bastam/');
$path4 = $convert->convert('?ex=151.0_5'); // https://bastam.bankmellat.ir/bastam/?ex=151.0_5
$path5 = $convert->convert('http://www.example.com/bastam/resources/images/browserSupport/war_icon.png'); 
// http://www.example.com/bastam/resources/images/browserSupport/war_icon.png
$path6 = $convert->convert('javascript:void(0)'); // ''
```

## Methods

### __construct($pagePath)
The constructor function accepts the page path in the string form, it's clear that page path must be absolute and you can also change this path with the setPagePath method 

### setBaseTag(string $baseTag): void
If your page uses of base tag you can set this path as a string with the setBaseTag parameter.

### setPagePath(string $pagePath): void
If you want to change the page path that has already been set. You can use this method.

### convert($path)
 After set page path and if there is a base tag, you use Convert to convert any path to an absolute path. $path can be empty, absolute, with ./ or ../, has fragment or query string and so on.
 
 