<?php

use Tabus\Client;

require __DIR__ . '/../vendor/autoload.php';

// Передаем токен (key) и актуальный домен.
$client = new Client('135863afef0756ed866ecc3ed872371d', 'https://api1651726272.bhcesh.me');

// Опционально: кэшируем последущие запросы на 1 час.
$client->setCache(__DIR__ . '/cache', 3600);

// Получаем информацию о фильме.
$response = $client->api('franchise/details', ['id' => 1]);

// Выводим информацию о фильме.
dump($response->name . ' — ' . $response->slogan);