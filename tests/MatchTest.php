<?php

namespace Skollro\Otherwise\Test;

use Exception;
use Skollro\Otherwise\Match;
use PHPUnit\Framework\TestCase;

class MatchTest extends TestCase
{
    /** @test */
    public function match_helper_creates_a_new_instance()
    {
        $match = match('A');

        $this->assertInstanceOf(Match::class, $match);
    }

    /** @test */
    public function when_accepts_bool_condition()
    {
        $result = match(null)
            ->when(true, true)
            ->otherwise(false);

        $this->assertTrue($result);
    }

    /** @test */
    public function when_accepts_callback_condition()
    {
        $result = match([1, 2, 3])
            ->when(function ($value) {
                return count($value) < 3;
            }, true)
            ->otherwise(false);

        $this->assertFalse($result);
    }

    /** @test */
    public function when_accepts_function_condition()
    {
        $result = match([1, 2, 3])
            ->when('is_string', true)
            ->otherwise(false);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_returns_the_first_match()
    {
        $result = match(null)
            ->when(true, 'A')
            ->when(true, 'B')
            ->otherwise('C');

        $this->assertEquals('A', $result);
    }

    /** @test */
    public function otherwise_returns_given_value()
    {
        $result = match(null)
            ->when(false, true)
            ->otherwise(false);

        $this->assertFalse($result);
    }

    /** @test */
    public function otherwise_executes_given_callback()
    {
        $result = match([1, 2, 3])
            ->when(false, 42)
            ->otherwise(function ($value) {
                return count($value);
            });

        $this->assertSame(3, $result);
    }

    /** @test */
    public function callback_results_are_executed()
    {
        $result = match([1, 2, 3])
            ->when(true, function ($value) {
                return count($value);
            })
            ->otherwise(42);

        $this->assertSame(3, $result);
    }

    /** @test */
    public function it_throws_a_given_exception()
    {
        try {
            $result = match(null)
                ->when(false, null)
                ->otherwiseThrow(new Exception);
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            return;
        }

        $this->fail();
    }

    /** @test */
    public function it_throws_an_exception_lazily_by_default()
    {
        try {
            $result = match(null)
                ->when(false, null)
                ->otherwiseThrow(Exception::class);
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            return;
        }

        $this->fail();
    }

    /** @test */
    public function it_throws_an_exception_supplied_by_a_callback()
    {
        try {
            $result = match(42)
                ->when(false, null)
                ->otherwiseThrow(function ($value) {
                    $this->assertSame(42, $value);
                    return new Exception;
                });
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            return;
        }

        $this->fail();
    }
}
