<?php

namespace Usdtcloud\TronService;


use GuzzleHttp\Client;

class NodeClient
{
    protected $client;

    public static function mainNet()
    {
        return new self('https://api.trongrid.io');
    }

    public static function testNet()
    {
        return new self('https://api.shasta.trongrid.io');
    }

    public function __construct($uri)
    {
        $opts         = [
            'base_uri' => $uri,
        ];
        $this->client = new Client($opts);
    }

    public function post($api, $payload = [])
    {
        $opts = [
            'json' => $payload,
        ];
        $rsp  = $this->client->post($api, $opts);
        return $this->handle($rsp);
    }

    public function get($api, $query = [])
    {
        $opts = [
            'query' => $query,
        ];
        $rsp  = $this->client->get($api, $opts);
        return $this->handle($rsp);
    }

    public function handle($rsp)
    {
        $content = $rsp->getBody();
        $result = json_decode($content);
        if (is_null($result)){
            $result = json_decode($this->JsonStrFormat($content));
        }
        return $result;
    }

    public function JsonStrFormat($content)
    {
        $friend=iconv("utf-8","gbk//IGNORE",$content);
        return mb_convert_encoding($friend,"UTF-8","GBK");
    }

    public function version()
    {
        return '1.0.0';
    }
}