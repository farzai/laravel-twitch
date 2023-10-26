<?php

namespace Farzai\Twitch\Models\Traits;

trait HasPagination
{
    /**
     * @var array<string, mixed>
     */
    protected $pagination = [];

    /**
     * @return $this
     */
    public function setPagination(array $pagination)
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    public function hasMorePages()
    {
        return $this->pagination['cursor'] ?? false;
    }

    public function getNextPage()
    {
        return $this->pagination['cursor'] ?? null;
    }
}
