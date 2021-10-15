<?php

namespace Kaadon\TronService;


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
        //echo $content . PHP_EOL;
        return json_decode($content);
    }

    public function version()
    {
        return '1.0.0';
    }
}