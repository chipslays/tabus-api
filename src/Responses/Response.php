<?php

namespace Tabus\Responses;

use Tabus\Support\Collection;

class Response extends Collection
{
    /**
     * @param array|stdClass $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        // стандартизируем ответ
        $this->items = $this->standartize($this->items);

        if ($this->items['results']) {
            $this->items['results'] = $this->standartize($this->items['results']);
        }
    }

    /**
     * Удаляет мусорные символы, стандартизирует массив.
     *
     * @param array $items
     * @return array
     */
    public function standartize(array $items): array
    {
        foreach ($items as &$item) {
            $values = [
                '-','–','—', [], '',
            ];
            if (in_array($item, $values)) {
                $item = null;
            }
        }

        return $items;
    }

    /**
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return isset($this->items['next_page']) && $this->items['next_page'] !== null;
    }

    /**
     * @return bool
     */
    public function hasPrevPage(): bool
    {
        return isset($this->items['prev_page']) && $this->items['prev_page'] !== null;
    }

    /**
     * @return self|null
     */
    public function getNextPage(): self|null
    {
        return $this->hasNextPage() ? $this->client->raw($this->items['next_page']) : null;
    }

    /**
     * @return self|null
     */
    public function getPrevPage(): self|null
    {
        return $this->hasPrevPage() ? $this->client->raw($this->items['prev_page']) : null;
    }
}
