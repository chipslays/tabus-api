<?php

use Tabus\Client;
use Tabus\Constants\Format;

require __DIR__ . '/../vendor/autoload.php';

// Передаем токен (key) и актуальный домен.
$client = new Client('135863afef0756ed866ecc3ed872371d', 'https://api1651726272.bhcesh.me');

// Опционально: кешируем последущие запросы на 1 час.
$client->cache(__DIR__ . '/cache', 3600);

// Получаем жанры.
$response = $client->api('genre');

// Выводим жанры со всех страниц.
while ($response = $response->getNextPage()) {
    $response->results->each(function ($item) {
        dump($item['name']);
    });
};
