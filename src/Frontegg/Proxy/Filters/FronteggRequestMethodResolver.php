<?php

namespace Frontegg\Proxy\Filters;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Create;
use Psr\Http\Message\RequestInterface;

class FronteggRequestMethodResolver implements FilterInterface
{
    /**
     * @param callable $handler
     * 
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            if ($request->getMethod() !== 'OPTIONS') {
                return $handler($request, $options);
            }

            return Create::promiseFor(new Response(204));
        };
    }
}
