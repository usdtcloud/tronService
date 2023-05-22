<?php

namespace Usdtcloud\TronService;


use Exception;

/**
 *
 */
class TronKit
{
    /**
     * 合约ABI
     * @var
     */
    public $api;
    /**
     * 账户实例
     * @var mixed|null
     */
    public $credential;

    /**
     * @param $tronApi
     * @param $credential
     */
    public function __construct($tronApi, $credential = null)
    {
        $this->api        = $tronApi;
        $this->credential = $credential;
    }

    /**
     * @param $credential
     * @return void
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
    }

    /**
     * @return mixed|null
     * @throws Exception
     */
    public function getCredential()
    {
        if (is_null($this->credential)) {
            throw new Exception('Credential not set.');
        }
        return $this->credential;
    }

    /**
     * @param $to
     * @param $amount
     * @return mixed
     * @throws Exception
     */
    public function sendTrx($to, $amount)
    {
        $credential = $this->getCredential();
        $from       = $credential->address()->base58();

        $tx       = $this->api->createTransaction($to, $amount, $from);
        $signedTx = $credential->signTx($tx);
        $ret      = $this->api->broadcastTransaction($signedTx);
        return $ret;
    }

    /**
     * @param $tx
     * @return mixed
     */
    public function broadcast($tx)
    {
        return $this->api->broadcastTransaction($tx);
    }

    /**
     * @param $address
     * @return mixed
     */
    public function getTrxBalance($address)
    {
        return $this->api->getBalance($address);
    }

    /**
     * @param $abi
     * @return Contract
     * @throws Exception
     */
    public function contract($abi)
    {
        $credential = $this->getCredential();
        return new Contract($this->api, $abi, $credential);
        return $inst;
    }


    /**
     * @param $contract_address //合约地址
     * @return Trc20
     * @throws Exception
     */
    public function trc20($contract_address)
    {
        $credential = $this->getCredential();
        $inst       = new Trc20($this->api, $credential);
        return $inst->at($contract_address);
    }
}