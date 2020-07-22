<?php

namespace utils;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class DingTalk
{

    private static $webHookUrl;

    private static $secretkey;

    const MYSQL_SYNC_TO_ES = [
        'url' => 'https://oapi.dingtalk.com/robot/send?access_token=082a413e1bb1334efe76760d73f0d6cc630e38645a0320f6cbc8cdc43b037797',
        'key' => 'SEC420ab18582487d49b736e5d6bb11651b028c1cc4921e87a384f438fa8c0e03ad',
    ];

    public function __construct(array $dingIde)
    {
        if (!isset($dingIde['url']) || !isset($dingIde['key']))
            throw new \Exception('参数错误');

        self::$webHookUrl = $dingIde['url'];
        self::$secretkey = $dingIde['key'];
    }


    public function formatErrorSendDingTalkMsg(\Exception $exception, $at = false)
    {
        $message = "";

        if (isset($exception->project))
            $message .= "project：{$exception->project}" . PHP_EOL;

        $message .= "code：{$exception->getCode()}" . PHP_EOL .
            "file：{$exception->getFile()}" . PHP_EOL .
            "line：{$exception->getLine()}" . PHP_EOL .
            "message：{$exception->getMessage()}" . PHP_EOL;


        if (isset($exception->desc))
            $message .= "desc：{$exception->desc}";

        return $this->sendDingTalkMsg($message, $at);
    }


    public function sendDingTalkMsg($message, $at = false)
    {

        $params = [
            'msgtype' => 'text',
            'text' => [
                'content' => $message
            ],
            'at' => [
                'atMobiles' => [],
                'isAtAll' => false
            ],
        ];

        if ($at === true)
            $params["at"]["isAtAll"] = true;
        elseif (is_array($at) && $at)
            $params["at"]["atMobiles"] = $at;

        $headers = [
            'Content-Type' => 'application/json',
            'charset' => 'utf-8',
        ];

        $sign = $this->sign($timestamp);
        $url = self::$webHookUrl . "&timestamp={$timestamp}&sign={$sign}";

        try {
            require_once APP_ROOT . 'libary/guzzle/vendor/autoload.php';
            $client = new Client([
                'base_uri' => $url,
                'timeout' => 3,
                'connect_timeout' => 3
            ]);

            $request = new Request('post', $url, $headers, json_encode($params));
            $response = $client->send($request);

            $responseContentStr = $response->getBody()->__toString();
            $responseContentData = json_decode($responseContentStr, true);
            if (json_last_error())
                throw new \Exception(json_last_error_msg());

            if (!isset($responseContentData['errcode']))
                throw new \Exception("解析响应数据失败;response={$responseContentStr}");

            if ($responseContentData['errcode'] != 0)
                throw new \Exception("发送dingtalk消息失败;response={$responseContentStr}");

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }


    private function sign(&$timestamp = null)
    {
        $timestamp = $this->getMsecTimestamp();

        $str = "{$timestamp}\n" . self::$secretkey;

        $sign = hash_hmac("sha256", $str, self::$secretkey, true);
        $sign = urlencode(utf8_encode(base64_encode($sign)));

        return $sign;
    }

    /**
     *
     * 获取毫秒时间戳
     *
     * @return float
     */

    private function getMsecTimestamp()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

}