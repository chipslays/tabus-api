# 🎬 Tabus API

Низкоуровневая библиотека для работы с API Tabus, с поддержкой простого кэширования запросов из коробки.

# Установка

```bash
composer require chipslays/tabus-api
```

# Использование

```php
use Tabus\Client;

require __DIR__ . '/vendor/autoload.php';

// Передаем токен (key) и актуальный домен.
$client = new Client('xxx', 'https://api1234567890.example.com');

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
```

# Примеры

Примеры можно найти [здесь](/examples).

# Лицензия

MIT