<?php

namespace Usdtcloud\TronService;


use Elliptic\EC;
use kornrunner\Keccak; 

class Credential
{
    protected $keyPair;

    public function __construct($privateKey)
    {
        $ec            = new EC('secp256k1');
        $this->keyPair = $ec->keyFromPrivate($privateKey);
    }

    public static function fromPrivateKey($privateKey)
    {
        return new self($privateKey);
    }

    public static function create()
    {
        $bin        = microtime() . md5(microtime()) . sha1('TronAddress_' . md5(microtime()) . rand(11111111, 999999999) . microtime());
        $privateKey = bin2hex($bin);
        return new self($privateKey);
    }

    public function privateKey()
    {
        return $this->keyPair->getPrivate()->toString(16, 2);
    }

    public function publicKey()
    {
        return $this->keyPair->getPublic()->encode('hex');
    }

    public function address()
    {
        return Address::fromPublicKey($this->publicKey());
    }

    public function sign($hex)
    {
        $signature = $this->keyPair->sign($hex);
        $r         = $signature->r->toString('hex');
        $s         = $signature->s->toString('hex');
        $v = bin2hex(chr($signature->recoveryParam));
        return $r . $s . $v;
    }

    public function signTx($tx)
    {
        $signature = $this->sign($tx->txID);
        $tx->signature = [$signature];
        return $tx;
    }
}