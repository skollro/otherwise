<?php

namespace Skollro\Otherwise;

use Throwable;

class Match
{
    protected $value;
    protected $result;
    protected $hasMatch;

    protected function __construct($value)
    {
        $this->value = $value;
        $this->result = null;
        $this->hasMatch = false;
    }

    public static function value($value)
    {
        return new self($value);
    }

    public function when($condition, $result)
    {
        if ($this->hasMatch) {
            return $this;
        }

        if (is_callable($condition) ? $condition($this->value) : $condition) {
            $this->result = $result;
            $this->hasMatch = true;
        }

        return $this;
    }

    public function whenInstanceOf($type, $result)
    {
        return $this->when($this->value instanceof $type, $result);
    }

    public function otherwise($value)
    {
        return $this->resolveResult(function () use ($value) {
            return is_callable($value) ? $value($this->value) : $value;
        });
    }

    public function otherwiseThrow($value)
    {
        return $this->resolveResult(function () use ($value) {
            throw (new self($value))
                ->when(is_callable($value), function ($value) {
                    return $value($this->value);
                })
                ->when($value instanceof Throwable, $value)
                ->otherwise(function ($value) {
                    return new $value;
                });
        });
    }

    protected function resolveResult($otherwise)
    {
        if ($this->hasMatch) {
            return is_callable($this->result) ? ($this->result)($this->value) : $this->result;
        }

        return $otherwise();
    }
}
