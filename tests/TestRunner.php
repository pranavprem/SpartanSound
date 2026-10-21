<?php

declare(strict_types=1);

namespace SpartanSound;

final class TestRunner
{
    private int $failures = 0;

    public function check(bool $condition, string $message): void
    {
        if ($condition) {
            echo "ok  $message\n";
            return;
        }
        $this->failures++;
        echo "FAIL $message\n";
    }

    public function same(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected === $actual) {
            $this->check(true, $message);
            return;
        }
        $this->check(
            false,
            $message . ' expected ' . var_export($expected, true) . ' got ' . var_export($actual, true)
        );
    }

    public function finish(string $label): void
    {
        if ($this->failures > 0) {
            fwrite(STDERR, $this->failures . " failed ($label)\n");
            exit(1);
        }
        echo "all $label passed\n";
    }
}
