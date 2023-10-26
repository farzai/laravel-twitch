<?php

namespace Farzai\Twitch;

use Farzai\Twitch\Models\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Builder
{
    /**
     * The HTTP Client to request data from the API.
     */
    private PendingRequest $client;

    /**
     * The endpoint of the API that should be requested.
     */
    private string $endpoint;

    /**
     * The model that should be used.
     */
    private Model $model;

    /**
     * The query data that should be attached to the request.
     */
    private Collection $query;

    /**
     * Builder constructor.
     *
     * @throws \Exception
     */
    public function __construct(mixed $model = null)
    {
        if ($model) {
            if (is_string($model)) {
                $model = new $model;
            }

            if (! is_subclass_of($model, Model::class)) {
                throw new \Exception('Model must be an instance of '.Model::class);
            }

            $this->setModel($model);

            $this->setEndpoint($model->getEndpoint());
        }

        $this->initializeClient();
        $this->initializeQuery();
    }

    public function search(string $query)
    {
        $this->query->put('queryParams', array_merge($this->query->get('queryParams'), [
            'query' => $query,
        ]));

        return $this;
    }

    public function where(string $key, mixed $value)
    {
        $this->query->put('queryParams', array_merge($this->query->get('queryParams'), [
            $key => $value,
        ]));

        return $this;
    }

    public function orWhere(string $key, mixed $value)
    {
        // Check if the key already exists in the query params.
        $value = [...Arr::wrap($this->query->get('queryParams')[$key]), $value];

        $this->query->put('queryParams', array_merge($this->query->get('queryParams'), [
            $key => $value,
        ]));

        return $this;
    }

    public function whereIn(string $key, mixed $values)
    {
        $this->query->put('queryParams', array_merge($this->query->get('queryParams'), [
            $key => $values,
        ]));

        return $this;
    }

    public function orderBy(string $key, string $direction = 'asc')
    {
        $this->query->put('queryParams', array_merge($this->query->get('queryParams'), [
            'sort' => $key,
            'direction' => $direction,
        ]));

        return $this;
    }

    public function orderByDesc(string $key)
    {
        return $this->orderBy($key, 'desc');
    }

    public function take(int $limit)
    {
        $this->query->put('queryParams', array_merge($this->query->get('queryParams'), [
            'first' => $limit,
        ]));

        return $this;
    }

    public function get(): Collection
    {
        return $this->fetch()->collect('data')->map(function ($item) {
            return $this->model->newInstance($item);
        });
    }

    public function first()
    {
        return $this->take(1)->get()->first();
    }

    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    protected function fetch(): Response
    {
        if (! $this->endpoint) {
            throw new \Exception('Endpoint must be set.');
        }

        $params = [];
        foreach ($this->query->get('queryParams') as $key => $value) {
            if (is_countable($value)) {
                foreach ($value as $item) {
                    $params[] = "{$key}={$item}";
                }

                continue;
            }

            $params[] = "{$key}={$value}";
        }

        $queryParams = implode('&', $params);

        $response = $this->client
            ->withToken(Authorizer::retrieveAccessToken())
            ->retry(3, 100)
            ->get($this->endpoint.'?'.$queryParams)
            ->throw();

        return $response;
    }

    /**
     * Initialize the HTTP Client.
     */
    private function initializeClient(): void
    {
        $this->client = Http::withOptions([
            'base_uri' => 'https://api.twitch.tv/helix/',
        ])->withHeaders([
            'Accept' => 'application/json',
            'Client-ID' => config('twitch.credentials.client_id'),
        ]);
    }

    /**
     * Initialize the query data.
     */
    private function initializeQuery(): void
    {
        $this->query = collect([
            'queryParams' => [],
        ]);
    }

    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }
}
