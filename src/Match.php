<?php

namespace Skollro\Otherwise;

use Throwable;

class Match
{
    protected $value;
    protected $params;
    protected $result;
    protected $hasMatch;

    protected function __construct($value, ...$params)
    {
        $this->value = $value;
        $this->params = $params;
        $this->result = null;
        $this->hasMatch = false;
    }

    public static function value($value, ...$params)
    {
        return new self($value, ...$params);
    }

    public function when($condition, $result)
    {
        return $this->matchResult($condition, function () use ($result) {
            $this->result = $result;
            $this->hasMatch = true;
        });
    }

    public function whenInstanceOf($type, $result)
    {
        return $this->when($this->value instanceof $type, $result);
    }

    public function whenThrow($condition, $result)
    {
        return $this->matchResult($condition, function () use ($result) {
            throw $this->resolveThrowable($result);
        });
    }

    public function otherwise($value)
    {
        return $this->resolveResult(function () use ($value) {
            return is_callable($value) ? $value($this->value, ...$this->params) : $value;
        });
    }

    public function otherwiseThrow($value)
    {
        return $this->resolveResult(function () use ($value) {
            throw $this->resolveThrowable($value);
        });
    }

    protected function matchResult($condition, $when)
    {
        if ($this->hasMatch) {
            return $this;
        }

        if ($this->resolveCondition($condition)) {
            $when();
        }

        return $this;
    }

    protected function resolveCondition($condition)
    {
        if (is_bool($condition)) {
            return $condition;
        }

        if (is_callable($condition)) {
            return $condition($this->value);
        }

        return $condition == $this->value;
    }

    protected function resolveResult($otherwise)
    {
        if ($this->hasMatch) {
            return is_callable($this->result) ? ($this->result)($this->value, ...$this->params) : $this->result;
        }

        return $otherwise();
    }

    protected function resolveThrowable($value)
    {
        return (new self($value))
            ->when(is_callable($value), function ($value) {
                return $value($this->value, ...$this->params);
            })
            ->when($value instanceof Throwable, $value)
            ->otherwise(function ($value) {
                return new $value;
            });
    }
}
