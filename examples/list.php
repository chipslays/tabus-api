<?php

use Tabus\Client;

require __DIR__ . '/../vendor/autoload.php';

// Передаем токен (key) и актуальный домен.
$client = new Client('135863afef0756ed866ecc3ed872371d', 'https://api1651726272.bhcesh.me');

// Опционально: кешируем последущие запросы на 1 час.
$client->cache(__DIR__ . '/cache', 3600);

// Получаем фильмы.
$response = $client->api('list', ['limit' => 5, 'type' => 'films']);

// Выводим фильмы
$response->results->each(function ($item) {
    dump($item['name']);
});