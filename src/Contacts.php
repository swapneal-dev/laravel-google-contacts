<?php

namespace SwapnealDev\GoogleContact;

use Google\Exception;
use Google\Service\PeopleService;
use Illuminate\Support\Collection;
use SwapnealDev\GoogleContact\Exceptions\InvalidConfiguration;

class Contacts{
    /**
     * @throws InvalidConfiguration
     * @throws Exception
     */
    public static function get()
    {
        $googleContact = static::getGoogleContacts();

        $googleContactList = $googleContact->listContacts();

        $googleConnections = $googleContactList->getConnections();

        while ($googleContactList->getNextPageToken()) {
            $queryParameters['pageToken'] = $googleContactList->getNextPageToken();

            $googleContactList = $googleContact->listContacts($queryParameters);

            $googleConnections = array_merge($googleConnections, $googleContactList->getConnections());
        }

        return collect($googleConnections)->sortBy(function ($person, $index)  {
                return $person->names[0]->displayName;
            })->values();
    }

    /**
     * @throws InvalidConfiguration
     * @throws Exception
     */
    protected static function getGoogleContacts(): GoogleContact
    {
        if (!auth()->check()) {
            throw InvalidConfiguration::authenticationRequired();
        }

        return GoogleContactFactory::create();
    }
}
