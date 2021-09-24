<?php

require "vendor/autoload.php";

$a = \Kaadon\TronService\Credential::create()->address();
var_dump($a);
