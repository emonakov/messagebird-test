<?php
use Messagebird\App;
use Messagebird\Model\Mode;
use Messagebird\Controller\Message;

require __DIR__.'/vendor/autoload.php';

$app = App::getApp();

$app->initMode(new Mode());

$controller = new Message(App::getApp()->getApiKey());
$controller->processMessage();
$controller->sendMessage();

