<?php

namespace Frontegg\Proxy\Filters;

use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

class FronteggRequestHeaderResolver implements FilterInterface
{
    /**
     * @param callable $handler
     * 
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            if (
                !$this->getBodyContents($request)
                || in_array('application/json', $request->getHeader('Content-Type'))
            ) {
                return $handler($request, $options);
            }

            // In case if content-type is application/x-www-form-urlencoded
            // we need to change to application/json
            if (in_array('application/x-www-form-urlencoded', $request->getHeader('Content-Type'))) {
                $request = $this->setJsonDataToRequestBody($request);
            }
            $request = $request->withHeader('Content-Type', 'application/json');

            return $handler($request, $options);
        };
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    protected function setJsonDataToRequestBody(RequestInterface $request): RequestInterface
    {
        $body = $this->getBodyContents($request);
        parse_str($body, $data);
        $stream = Utils::streamFor(json_encode($data));

        return $request->withBody($stream);
    }

    /**
     * Returns body contents.
     * Rewinds stream pointer back.
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    protected function getBodyContents(RequestInterface $request): string
    {
        $body = $request->getBody()->getContents();
        $request->getBody()->rewind();

        return $body;
    }
}
