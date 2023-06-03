<?php

namespace Usdtcloud\TronService;


use Elliptic\EC;
use kornrunner\Keccak;

/**
 *
 */
class Credential
{
    /**
     * @var EC\KeyPair
     */
    protected $keyPair;
    /**
     * @var bool
     */
    protected $multiSign = false;

    /**
     * @param $privateKey
     */
    public function __construct($privateKey, ?bool $multiSign = null)
    {
        if ($this->multiSign) {
            $this->multiSign = $multiSign;
        }

        $ec            = new EC('secp256k1');
        $this->keyPair = $ec->keyFromPrivate($privateKey);

    }


    /**
     * @param $privateKey
     * @return Credential
     */
    public static function fromPrivateKey($privateKey)
    {
        return new self($privateKey);
    }

    /**
     * @return Credential
     */
    public static function create()
    {
        $bin        = microtime() . md5(microtime()) . sha1('TronAddress_' . md5(microtime()) . rand(11111111, 999999999) . microtime());
        $privateKey = bin2hex($bin);
        return new self($privateKey);
    }

    /**
     * @return mixed
     */
    public function privateKey()
    {
        return $this->keyPair->getPrivate()->toString(16, 2);
    }

    /**
     * @return mixed
     */
    public function publicKey()
    {
        return $this->keyPair->getPublic()->encode('hex');
    }

    /**
     * @return Address
     */
    public function address()
    {
        return Address::fromPublicKey($this->publicKey());
    }

    /**
     * @param $hex
     * @return string
     */
    public function sign($hex)
    {
        $signature = $this->keyPair->sign($hex);
        $r         = $signature->r->toString('hex');
        $s         = $signature->s->toString('hex');
        $v         = bin2hex(chr($signature->recoveryParam));
        return $r . $s . $v;
    }

    /**
     * @param $tx
     * @return mixed
     */
    public function signTx($tx)
    {
        $signature     = $this->sign($tx->txID);
        if (isset($tx->signature)){
            $tx->signature[count($tx->signature)] = $signature;
        }else{
            $tx->signature = [$signature];
        }
        return $tx;
    }
}