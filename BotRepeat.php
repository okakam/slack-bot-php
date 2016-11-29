<?php
require 'vendor/autoload.php';
require_once 'BotConfig.php';
date_default_timezone_set('Asia/Tokyo');

$loop = React\EventLoop\Factory::create();

$client = new Slack\RealTimeClient($loop);
$client->setToken(BotConfig::SLACK_TOKEN);

$client->on('message', function ($data) use ($client) {
    print_r("Someone typed a message: " . $data['text'] . " : " . $data['channel'] . "\n");

    $client->getChannelById($data['channel'])->then(function ($chanel) use ($client, $data) {
        $client->send($data['text'], $chanel);
    });
});

$client->connect()->then(function () {
    print_r('Connect !' . "\n");
});

$loop->run();
