<?php

namespace Tabus\Responses;

use Tabus\Support\Collection;

class Response extends Collection
{
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
