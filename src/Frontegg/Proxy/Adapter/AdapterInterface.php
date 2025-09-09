<?php

namespace Frontegg\Proxy\Adapter;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface AdapterInterface
{
    /**
     * Allow this adapter to be used as a Guzzle handler.
     *
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface;

    /**
     * Send the request and return the response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function send(RequestInterface $request): ResponseInterface;
}
