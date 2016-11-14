<?php
require 'vendor/autoload.php';
require_once 'BotConfig.php';
date_default_timezone_set('Asia/Tokyo');

$loop = React\EventLoop\Factory::create();

$client = new Slack\RealTimeClient($loop);
$client->setToken(BotConfig::TOKEN);

$client->on('message', function ($data) use ($client) {
    echo "Someone typed a message: " . $data['text'] . " : " . $data['channel'] . "\n";

    $client->getChannelById($data['channel'])
        ->then(function ($chanel) use ($client) {
            $client->send('TESTTEST', $chanel);
        });
});

$client->connect()->then(function () {
    echo "Connected!\n";
});

$loop->run();