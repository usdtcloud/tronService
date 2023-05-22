<?php

namespace Usdtcloud\TronService;


use GuzzleHttp\Client;
use think\facade\Config;

class NodeClient
{
    protected $client;
    protected $api_key;

    public static function mainNet(?string $apikey = null)
    {
        return new self('https://api.trongrid.io',$apikey);
    }

    public static function testNet(?string $apikey = null)
    {
        return new self('https://api.shasta.trongrid.io',$apikey);
    }

    public function __construct(string $uri, ?string $apikey = null)
    {
        if (is_null($apikey)) {
            $is_apikey = Config::has('tronservice.api_key');
            if ($is_apikey) {
                $this->api_key = Config::get('tronservice.api_key');
            }
        } else {
            $this->api_key = $apikey;
        }
        $opts = [
            'base_uri' => $uri,
        ];
        if ($this->api_key) {
            $opts['headers'] = ["TRON-PRO-API-KEY" => $this->api_key];
        }
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
        $result  = json_decode($content);
        if (is_null($result)) {
            $result = json_decode($this->JsonStrFormat($content));
        }
        return $result;
    }

    public function JsonStrFormat($content)
    {
        $friend = iconv("utf-8", "gbk//IGNORE", $content);
        return mb_convert_encoding($friend, "UTF-8", "GBK");
    }

    public function version()
    {
        return '1.0.0';
    }
}