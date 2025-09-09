<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Exception\AuthenticationException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

class FronteggRequestAuthHeaderResolver implements FilterInterface
{
    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var callable
     */
    protected $contextResolver;

    /**
     * FronteggRequestAuthHeaderResolver constructor.
     *
     * @param Authenticator $authenticator
     * @param callable      $contextResolver
     */
    public function __construct(Authenticator $authenticator, callable $contextResolver)
    {
        $this->authenticator = $authenticator;
        $this->contextResolver = $contextResolver;
    }

    /**
     * @param callable $handler
     * 
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            if (!$this->authenticator->getAccessToken()) {
                throw new AuthenticationException('Authentication problem');
            }

            $context = $this->getResolvedContext($request);

            $request = $request->withHeader(
                'x-access-token',
                $this->authenticator->getAccessToken()->getValue()
            );
            $request = $request->withHeader(
                'frontegg-tenant-id',
                $context['tenantId'] ?? ''
            );
            $request = $request->withHeader(
                'frontegg-user-id',
                $context['userId'] ?? ''
            );

            return $handler($request, $options);
        };
    }

    /**
     * @param RequestInterface $request
     *
     * @return array
     */
    protected function getResolvedContext(RequestInterface $request): array
    {
        return call_user_func($this->contextResolver, $request);
    }
}
