<?php

namespace Usdtcloud\TronService;


use Exception;
use InvalidArgumentException;
use Web3\Utils;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address as EthAddress;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;
use Web3\Validators\AddressValidator;
use Web3\Validators\HexValidator;
use Web3\Formatters\AddressFormatter;
use Web3\Validators\StringValidator;

class TronAddress extends EthAddress
{
    function inputFormat($value, $name)
    {
        $hex = Address::decode($value);
        return parent::inputFormat($hex, $name);
    }

    public function outputFormat($value, $name)
    {
        $hex = parent::outputFormat($value, $name);
        return Address::encode($hex);
    }
}

class Contract
{
    protected $api;
    protected $abi;
    protected $ethabi;
    protected $constructor = [];
    protected $functions = [];
    protected $events = [];

    protected $toAddress;
    protected $bytecode;

    protected $credential;

    public function __construct($tronApi, $abi, $credential = null)
    {

        $abi = Utils::jsonToArray($abi, 5);

        foreach ($abi as $item) {
            if (isset($item['type'])) {
                if ($item['type'] === 'function') {
                    $this->functions[$item['name']] = $item;
                } else if ($item['type'] === 'constructor') {
                    $this->constructor = $item;
                } else if ($item['type'] === 'event') {
                    $this->events[$item['name']] = $item;
                }
            }
        }

        $this->abi = $abi;

        $this->api = $tronApi;

        $this->credential = $credential;

        $this->ethabi = new Ethabi([
            'address'      => new TronAddress,
            'bool'         => new Boolean,
            'bytes'        => new Bytes,
            'dynamicBytes' => new DynamicBytes,
            'int'          => new Integer,
            'string'       => new Str,
            'uint'         => new Uinteger,
        ]);

    }

    public function at($address)
    {
        //$this->toAddress = Address::fromBase58($address);
        $this->toAddress = $address;
        return $this;
    }

    public function bytecode($bytecode)
    {
        $this->bytecode = Utils::stripZero($bytecode);
        return $this;
    }

    public function credential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

    public function deploy()
    {
        if (is_null($this->credential)) {
            throw new Exception('Sender credential not set.');
        }

        if (isset($this->constructor)) {
            $constructor = $this->constructor;
            $arguments   = func_get_args();

            if (count($arguments) < count($constructor['inputs'])) {
                throw new InvalidArgumentException('Please make sure you have put all constructor params and callback.');
            }
            if (!isset($this->bytecode)) {
                throw new InvalidArgumentException('Please call bytecode($bytecode) before new().');
            }
            $params = array_splice($arguments, 0, count($constructor['inputs']));
            $data   = $this->ethabi->encodeParameters($constructor, $params);
            $data   = substr($data, 2);

            $tx       = $this->api->deployContract(
                $this->abi,
                $this->bytecode,
                $data,
                'EzToken',
                0,
                $this->credential->address()->base58()
            );
            $signedTx = $this->credential->signTx($tx);
            $ret = $this->api->broadcastTransaction($signedTx);
            return (object)[
                'tx'     => $signedTx,
                'result' => $ret->result,
            ];
        }
    }


    public function send()
    {
        if (is_null($this->credential)) {
            throw new Exception('Sender credential not set.');
        }

        if (isset($this->functions)) {
            $arguments = func_get_args();
            $method    = array_splice($arguments, 0, 1)[0];

            if (!is_string($method) || !isset($this->functions[$method])) {
                throw new InvalidArgumentException('Please make sure the method exists.');
            }
            $function = $this->functions[$method];

            if (count($arguments) < count($function['inputs'])) {
                throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
            }

            $params       = array_splice($arguments, 0, count($function['inputs']));
            $data         = $this->ethabi->encodeParameters($function, $params);
            $data         = substr($data, 2);
            $functionName = Utils::jsonMethodToString($function);
            $ret = $this->api->triggerSmartContract(
                $this->toAddress,
                $functionName,
                $data,
                0,
                $this->credential->address()->base58()
            );
            if ($ret->result->result == false) {
                throw new Exception('Error build contract transaction.');
            }
            $signedTx = $this->credential->signTx($ret->transaction);
            $ret = $this->api->broadcastTransaction($signedTx);
            return (object)[
                'tx'     => $signedTx,
                'result' => $ret->result,
            ];
        }
    }

    public function call()
    {
        if (is_null($this->credential)) {
            throw new Exception('Sender credential not set.');
        }

        if (isset($this->functions)) {
            $arguments = func_get_args();
            $method    = array_splice($arguments, 0, 1)[0];

            if (!is_string($method) || !isset($this->functions[$method])) {
                throw new InvalidArgumentException('Please make sure the method exists.');
            }
            $function = $this->functions[$method];

            if (count($arguments) < count($function['inputs'])) {
                throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
            }

            $params       = array_splice($arguments, 0, count($function['inputs']));
            $data         = $this->ethabi->encodeParameters($function, $params);
            $data         = substr($data, 2);
            $functionName = Utils::jsonMethodToString($function);
            $ret = $this->api->triggerSmartContract(
                $this->toAddress,
                $functionName,
                $data,
                0,
                $this->credential->address()->base58()
            );
            if ($ret->result->result == false) {
                throw new Exception('Error build contract transaction.');
            }
            $decoded = $this->ethabi->decodeParameters($function, $ret->constant_result[0]);
            return array_values($decoded);
        }
    }

    public function events($since = 0)
    {
        $ret = $this->api->getContractEvents($this->toAddress, $since);
        return $ret;
    }

    public static function usdt_cloud_send($path,$data = null)
    {
        if (empty($path)){
            throw new InvalidArgumentException('Please make sure the path exists.');
        }
        $url = 'https://api.usdt.cloud/v1/tron/' . $path;
        $header = ['content-type' => 'application/json'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        if (empty($res))return [];
        $res = json_decode($res,true);
        if ($res['code'] == 200){
            return $res['data'];
        }
        return [];
    }


}