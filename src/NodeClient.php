<?php

namespace Usdtcloud\TronService;


use GuzzleHttp\Client;
use think\Config;
use think\Env;
use Usdtcloud\TronService\Exception\TronException;

class NodeClient
{
    protected $client;
    protected $api_key;

    public static function mainNet(?string $apikey = null)
    {
        return new self('https://api.trongrid.io', $apikey);
    }

    public static function testNet(?string $apikey = null)
    {
        return new self('https://api.shasta.trongrid.io', $apikey);
    }

    public function array_rand_value(array $array, int $num = 1)
    {
        $value = null;
        if (is_array($array)) {
            if ($num >= count($array)) {
                return $array;
            }
            $array_rand = array_rand($array, $num);
            if ($num == 1) {
                $value = $array[$array_rand];
            } else {
                foreach ($array_rand as $item) {
                    $value[] = $array[$item];
                }
            }
        } else {
            return $array;
        }
        return $value;
    }

    public function getApiKey(): string
    {
        $api_key   = "";
        $is_apikey = (new Config())->has('tronservice.api_key');
        if ($is_apikey) {
            $api_key = (new Config())->get('tronservice.api_key');
            if (is_array($api_key)) {
                $api_key = $this->array_rand_value($api_key);
            }
        }
        return $api_key;
    }

    public function __construct(string $uri, ?string $apikey = null)
    {
        if (is_null($apikey)) {
            $this->api_key = $this->getApiKey();
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

        if (isset($result->Error)) {
            throw new TronException($result->Error);
        }
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