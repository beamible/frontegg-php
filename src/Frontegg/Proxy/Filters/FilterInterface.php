<?php

namespace Frontegg\Proxy\Filters;

interface FilterInterface
{
    public function __invoke(callable $handler): callable;
}
