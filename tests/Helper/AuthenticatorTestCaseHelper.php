<?php

namespace Frontegg\Tests\Helper;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggCurlHttpClient;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

abstract class AuthenticatorTestCaseHelper extends TestCase
{
    use ProphecyTrait;

    /**
     * @param FronteggHttpClientInterface $client
     * @param string                      $clientId
     * @param string                      $clientSecret
     * @param string                      $baseUrl
     * @param array                       $urls
     *
     * @return Authenticator
     */
    protected function createFronteggAuthenticator(
        FronteggHttpClientInterface $client,
        string $clientId = 'clientTestID',
        string $clientSecret = 'apiTestSecretKey',
        string $baseUrl = 'http://test',
        array $urls = [],
        bool $disbaleCors = true,
        ?callable $contextResolver = null,
        string $authenticationBaseUrl = 'http://auth'
    ): Authenticator {
        $contextResolver = $contextResolver ?? function () {
            return [];
        };
        $fronteggConfig = new Config(
            $clientId,
            $clientSecret,
            $baseUrl,
            $urls,
            $disbaleCors,
            $contextResolver,
            $authenticationBaseUrl
        );

        return new Authenticator($fronteggConfig, $client);
    }

    /**
     * @param array<ApiRawResponse> $authResponses
     *
     * @return FronteggHttpClientInterface
     */
    protected function createFronteggCurlHttpClientStub(
        array $authResponses = []
    ): FronteggHttpClientInterface {
        $client = $this->createStub(FronteggCurlHttpClient::class);
        $client->method('send')
            ->willReturnOnConsecutiveCalls(
                ...array_values($authResponses)
            );

        return $client;
    }

    /**
     * @param string $accessToken
     * @param int    $expiresIn
     * @param int    $httpStatusCode
     *
     * @return ApiRawResponse
     */
    protected function createAuthHttpApiRawResponse(
        string $accessToken = 'YOUR-JWT-TOKEN',
        int $expiresIn = 1800,
        int $httpStatusCode = 200
    ): ApiRawResponse {
        return new ApiRawResponse(
            [],
            sprintf(
                '{
                    "token": "%s",
                    "expiresIn": %d
                }',
                $accessToken,
                $expiresIn
            ),
            $httpStatusCode
        );
    }

    /**
     * @param int    $statusCode
     * @param string $error
     * @param string $message
     * @param int    $httpStatusCode
     *
     * @return ApiRawResponse
     */
    protected function createAuthFailureHttpApiRawResponse(
        int $statusCode = 401,
        string $error = 'Unauthorized',
        string $message = 'Could not verify vendor',
        int $httpStatusCode = 401
    ): ApiRawResponse {
        return new ApiRawResponse(
            [],
            sprintf(
                '{
                    "statusCode": %s,
                    "error": "%s",
                    "message": "%s"
                }',
                $statusCode,
                $error,
                $message
            ),
            $httpStatusCode
        );
    }
}
