<?php

namespace Tests;

use Illuminate\Support\Facades\Config;

trait ManipulatesConfig
{
    public function usingConfig(array $items, callable $fn)
    {
        try {
            $original = Config::getMany(array_keys($items));
            Config::set($items);
            return $fn();
        } finally {
            Config::set($original);
        }
    }
}
