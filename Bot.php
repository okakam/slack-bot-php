<?php
require 'vendor/autoload.php';
require_once 'BotConfig.php';
date_default_timezone_set('Asia/Tokyo');

$loop = React\EventLoop\Factory::create();

$client = new Slack\RealTimeClient($loop);
$client->setToken(BotConfig::TOKEN);

$client->getChannelById('C27J5H0UW')->then(function (\Slack\Channel $channel) use ($client) {
    $client->send('おくるよ', $channel);
});

// disconnect after first message
$client->on('message', function ($data) use ($client) {
    echo "Someone typed a message: ".$data['text']. " : " . $data['channel'] . "\n";
//    $client->disconnect();
});

$client->connect()->then(function () {
    echo "Connected!\n";
});

$loop->run();