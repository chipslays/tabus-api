<?php

use Tabus\Client;

require __DIR__ . '/../vendor/autoload.php';

// Передаем токен (key) и актуальный домен.
$client = new Client('135863afef0756ed866ecc3ed872371d', 'https://api1651726272.bhcesh.me');

// Опционально: кэшируем последущие запросы на 1 час.
$client->setCache(__DIR__ . '/cache', 3600);

// Опционально: кол-во страниц от текущей страницы
$client->setPaginate(2);

// Получаем фильмы.
$response = $client->api('list', ['page' => 10, 'limit' => 20, 'type' => 'films']);

// Выводим фильмы
$response->results->each(function ($item) {
    dump($item['name']);
});