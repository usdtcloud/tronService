<?php
require_once "vendor/autoload.php";

use Usdtcloud\TronService\Credential;
use Usdtcloud\TronService\TronKit;

function object_array($array)
{
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

$kit = new TronKit(
    \Usdtcloud\TronService\TronApi::nileNet(),
    Credential::fromPrivateKey("88a1154cf638fb35f436301c4aa30aad7ad450e62648bd778c97800d8278c17a")
//    Credential::fromPrivateKey("849cc9cd9514e70bdf624f3ba05360772c2b07a2a33998978e3e84571dd5cb7b")
);

$amount = 1 * 1000000;                                                            //转账金额，单位：SUN
$usdt   = $kit->Trc20("TLBaRhANQoJFTqre9Nf1mjuwNWjCJeYqUL");                      //创建Trc20代币合约实例
try {
    /*执行主体*/
    $ret        = $usdt->transfer("TVdBP1HePrJhsiwLc5LuygJXPztpUpFx9G", $amount, true);
//    $Credential = Credential::fromPrivateKey("849cc9cd9514e70bdf624f3ba05360772c2b07a2a33998978e3e84571dd5cb7b");
//    $tx         = $Credential->signTx($ret);
//    $bool       = \Usdtcloud\TronService\TronApi::nileNet()->broadcastTransaction($ret);
//    var_dump($bool);
} catch (\Exception $e) {

}

