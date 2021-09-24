<?php

require "vendor/autoload.php";

$a = \Kaadon\TronService\Credential::fromPrivateKey('69f8ecb1e005c0f85a0fd70a9a04a922bd972a5fbbc4326be27ed355a7e0391c')->address();
var_dump($a);
