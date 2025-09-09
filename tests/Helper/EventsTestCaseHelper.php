<?php

namespace Frontegg\Tests\Helper;

use Frontegg\Events\EventsClient;
use Frontegg\HttpClient\FronteggHttpClientInterface;

class EventsTestCaseHelper extends AuthenticatorTestCaseHelper
{
    /**
     * @param FronteggHttpClientInterface $client
     * @param string                      $clientId
     * @param string                      $clientSecret
     * @param string                      $baseUrl
     * @param array                       $urls
     *
     * @return EventsClient
     */
    public function createFronteggEventsClient(
        FronteggHttpClientInterface $client,
        string $clientId = 'clientTestID',
        string $clientSecret = 'apiTestSecretKey',
        string $baseUrl = 'http://test',
        array $urls = []
    ): EventsClient {
        $authenticator = $this->createFronteggAuthenticator(
            $client,
            $clientId,
            $clientSecret,
            $baseUrl,
            $urls
        );

        return new EventsClient($authenticator);
    }
}
