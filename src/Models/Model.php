<?php

namespace Farzai\Twitch\Models;

use ArrayAccess;
use Farzai\Twitch\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * Class Model
 *
 *
 * @property-read string $endpoint
 */
abstract class Model implements \IteratorAggregate, \JsonSerializable, ArrayAccess
{
    use ForwardsCalls,
        Traits\HasPagination;

    /**
     * The model's attributes.
     *
     * @var array
     */
    public $attributes = [];

    /**
     * Cast date attributes to Carbon instances.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

    /**
     * Model constructor.
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @return $this
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->dates)) {
                $value = new \Carbon\Carbon($value);
            }

            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->attributes[$this->getKeyName()] ?? null;
    }

    public function getKeyName(): string
    {
        return 'id';
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}(...$parameters);
        }

        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }

    public static function __callStatic($name, $arguments)
    {
        return (new static)->{$name}(...$arguments);
    }

    public function getEndpoint(): string
    {
        return Str::plural(Str::snake(class_basename($this)));
    }

    public static function query()
    {
        return (new static)->newQuery();
    }

    protected function newQuery(): Builder
    {
        return new Builder($this);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function newInstance(array $attributes = []): self
    {
        return new static($attributes);
    }

    public function __get(string $field): mixed
    {
        return $this->getAttribute($field);
    }

    public function __set(string $field, mixed $value): void
    {
        $this->attributes[$field] = $value;
    }

    /**
     * @param  mixed  $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]) || isset($this->relations[$offset]);
    }

    /**
     * @param  mixed  $offset
     */
    public function offsetGet($offset): mixed
    {
        return $this->getAttribute((string) $offset);
    }

    /**
     * @param  mixed  $offset
     * @param  mixed  $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * @param  mixed  $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset], $this->relations[$offset]);
    }

    public function __isset(mixed $field): bool
    {
        return $this->offsetExists($field);
    }

    public function __unset(mixed $field): void
    {
        $this->offsetUnset($field);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->attributes);
    }

    public function getAttribute(string $field): mixed
    {
        return collect($this->attributes)->get($field);
    }
}
