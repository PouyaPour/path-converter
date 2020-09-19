<?php

use seosazi\PathConverter\ConvertToAbsolutePath;

require_once "vendor/autoload.php";

$address = new ConvertToAbsolutePath('http://example.com/some/fake/path/');
var_dump($address->onlySitePath('example.com/some/fake'));
