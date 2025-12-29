<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('780519483648-ij027odr5lb2cqch8fathc05e0iri9d6.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/smartkampus/public/callback.php');

$client->addScope('email');
$client->addScope('profile');
