<?php

namespace SwapnealDev\GoogleContact;

use Google\Exception;
use Google\Service\PeopleService;
use SwapnealDev\GoogleContact\Exceptions\InvalidConfiguration;
use Google_Client;

class GoogleContactFactory{
    /**
     * @throws InvalidConfiguration
     * @throws Exception
     */
    public static function create(): GoogleContact
    {
        $config = config('google-contact');

        $client = self::createAuthenticatedGoogleClient($config);

        $service = new PeopleService($client);

        return self::createContactClient($service);
    }

    /**
     * @throws InvalidConfiguration|Exception
     */
    public static function createAuthenticatedGoogleClient(array $config): Google_Client
    {
        $authProfile = $config['default_auth_profile'];

        if ($authProfile === 'oauth') {
            return self::createOAuthClient($config['auth_profiles']['oauth']);
        }

        throw InvalidConfiguration::invalidAuthenticationProfile($authProfile);
    }

    /**
     * @throws Exception
     */
    protected static function createOAuthClient(array $authProfile): Google_Client
    {
        $client = new Google_Client;

        $client->setScopes([
            PeopleService::CONTACTS
        ]);

        $client->setAuthConfig($authProfile['credentials_json']);

        $client->setAccessToken(auth()->user()->getGoogleAccessToken());

        return $client;
    }

    protected static function createContactClient(PeopleService $service): GoogleContact
    {
        return new GoogleContact($service);
    }
}
