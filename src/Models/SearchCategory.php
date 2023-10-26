<?php

namespace Farzai\Twitch\Models;

class SearchCategory extends Model
{
    public function getEndpoint(): string
    {
        return 'search/categories';
    }
}
