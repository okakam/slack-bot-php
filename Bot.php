<?php
require 'vendor/autoload.php';
require_once 'BotConfig.php';
$_ = $_SERVER['_'];

date_default_timezone_set('Asia/Tokyo');
$chat_api_url = 'https://chatbot-api.userlocal.jp/api/chat';

$loop = React\EventLoop\Factory::create();

$client = new Slack\RealTimeClient($loop);
$client->setToken(BotConfig::SLACK_TOKEN);

$client->on('message', function ($data) use ($client) {
    print_r("Someone typed a message: " . $data['text'] . " : " . $data['channel'] . "\n");
    $message = null;
    if (preg_match('/^(..' . BotConfig::SLACK_BOT_HASH . '.)(.*)/', $data['text'], $matches)) {
        $message = isset($matches[2]) ? $matches[2] : null;
        $message = trim($message);
    }
    if (empty($message)) {
        return;
    }

    $response = "";
    // 天気と言われたら 東京の天気を返す
    if (preg_match('/^天気/', $message)) {
        $apiResponse = file_get_contents('https://rss-weather.yahoo.co.jp/rss/days/13.xml');
        $weatherXmlObj = simplexml_load_string($apiResponse);
        $response = '東京の天気 : ' . $weatherXmlObj->channel->item->description;
    } else {
        global $chat_api_url;
        $query = [
            'key' => BotConfig::CHAT_TOKEN,
            'message' => $message,
            'bot_name' => 'RoboPipin',
            'platform' => 'slack',
        ];
        $result = file_get_contents($chat_api_url . '?' . http_build_query($query));
        $resultArray = json_decode($result, true);

        if ($resultArray['status'] === 'success') {
            $response = $resultArray['result'];
        } else {
            $response = 'え？';
        }
    }

    if (empty($response)) {
        return;
    }

    $client->getChannelById($data['channel'])->then(function ($chanel) use ($client, $response) {
        $client->send($response, $chanel);
    });
});

$client->connect()->then(function () {
    print_r('Connect !' . "\n");
});

$loop->run();

print_r("Restart myself\n");
pcntl_exec($_, $argv);
