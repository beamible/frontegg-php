<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Http\Response as FronteggResponse;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggRetryRequestResolver implements FilterInterface
{
    protected const MAX_RETRY_COUNT = 3;

    protected Authenticator $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * @param callable $handler
     * 
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            $success = FronteggResponse::getSuccessHttpStatuses();

            $attempt = function (RequestInterface $req, int $left) use (&$attempt, $handler, $options, $success): PromiseInterface {
                return $handler($req, $options)->then(
                    function (ResponseInterface $response) use ($req, $left, &$attempt, $success): ResponseInterface|PromiseInterface {
                        if (in_array($response->getStatusCode(), $success, true)) {
                            return $response;
                        }

                        if ($left <= 0) {
                            return $response;
                        }

                        if ($response->getStatusCode() === FronteggResponse::HTTP_STATUS_UNAUTHORIZED) {
                            $this->authenticator->validateAuthentication();
                        }

                        return $attempt($req, $left - 1);
                    }
                );
            };

            return $attempt($request, static::MAX_RETRY_COUNT);
        };
    }
}
