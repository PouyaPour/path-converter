<?php

use seosazi\PathConverter\ConvertToAbsolutePath;

require_once "vendor/autoload.php";

$convert = new ConvertToAbsolutePath('https://www.jquery-az.com/html/test/demo.php?ex=151.0_5');
$path1 = $convert->convert('../../banana.jpg'); // https://www.jquery-az.com/banana.jpg
$path2 = $convert->convert('./bastam/shahab_service'); // https://www.jquery-az.com/html/test/bastam/shahab_service
$path3 = $convert->convert('browserSupport/war_icon.png'); // https://www.jquery-az.com/html/test/browserSupport/war_icon.png

 //and another example
$convert = new ConvertToAbsolutePath('https://www.example.com/bastam/shahab_service');
$convert->setBaseTag('https://bastam.bankmellat.ir/bastam/');
$path4 = $convert->convert('?ex=151.0_5'); // https://bastam.bankmellat.ir/bastam/?ex=151.0_5
$path5 = $convert->convert('http://www.example.com/bastam/resources/images/browserSupport/war_icon.png'); // http://www.example.com/bastam/resources/images/browserSupport/war_icon.png
$path6 = $convert->convert('javascript:void(0)'); // ''
