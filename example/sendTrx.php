<?php
require_once "vendor/autoload.php";
use Usdtcloud\TronService\Credential;
use Usdtcloud\TronService\TronKit;
//转账金额，单位：SUN
/** 获取签名的TRX交易 **/
//try {
//    $credential = new Credential("88a1154cf638fb35f436301c4aa30aad7ad450e62648bd778c97800d8278c17a");
//    $kit = new TronKit(
//        \Usdtcloud\TronService\TronApi::nileNet(),
//        $credential
//    );
//    $amount = 5 * 1000000;
//    /*执行主体*/
//    $ret = $kit->sendTrxData("TVdBP1HePrJhsiwLc5LuygJXPztpUpFx9G",156223355);                          //提交Trx转账交易
//} catch (\Exception $e) {
//    var_dump($e->getMessage());
//}
//
//var_dump($ret);

/** 发送TRX交易 **/
try {
    $credential = new Credential("88a1154cf638fb35f436301c4aa30aad7ad450e62648bd778c97800d8278c17a");
    $kit = new TronKit(
        \Usdtcloud\TronService\TronApi::nileNet(),
        $credential
    );
    $amount = 5 * 1000000;
    /*执行主体*/
    $ret = $kit->sendTrx("TVdBP1HePrJhsiwLc5LuygJXPztpUpFx9G",156223355);                          //提交Trx转账交易
} catch (\Exception $e) {
    var_dump($e->getMessage());
}

var_dump($ret);