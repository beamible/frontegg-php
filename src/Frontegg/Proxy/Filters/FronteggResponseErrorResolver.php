<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Http\Response as FronteggResponse;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggResponseErrorResolver implements FilterInterface
{
    protected const FRONTEGG_REQUEST_FAILED = 'Frontegg request failed';

    /**
     * @param callable $handler
     * 
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            return $handler($request, $options)->then(function (ResponseInterface $response) {
                if (in_array($response->getStatusCode(), FronteggResponse::getSuccessHttpStatuses())) {
                    return $response;
                }

                $response = $response->withStatus(FronteggResponse::HTTP_STATUS_INTERNAL_SERVER_ERROR);
                $response = $this->setServerErrorToResponse($response);
                return $response;
            });
        };
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function setServerErrorToResponse(ResponseInterface $response): ResponseInterface
    {
        $stream = Utils::streamFor(self::FRONTEGG_REQUEST_FAILED);

        return $response->withBody($stream);
    }
}
