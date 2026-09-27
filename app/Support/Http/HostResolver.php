<?php

namespace App\Support\Http;

interface HostResolver
{
    /**
     * IP addresses (v4 and v6) a hostname resolves to.
     *
     * @return string[]
     */
    public function resolve(string $host): array;
}
