<?php

require "vendor/autoload.php";

$a = \Kaadon\TronService\Credential::create()->privateKey();
var_dump($a);
$a = \Kaadon\TronService\Credential::fromPrivateKey('0880d6667c5bdc79af33c4e7df15e3f22fd2ab9cbd4d5d14e863e08504f1c509');