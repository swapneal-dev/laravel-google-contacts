<?php

namespace SwapnealDev\GoogleContact;

use Google\Service\PeopleService;

class GoogleContact
{
    protected PeopleService $contactService;

    public function __construct(PeopleService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function listContacts(array $queryParameters = []): PeopleService\ListConnectionsResponse
    {
        $parameters = array_merge($queryParameters, array('personFields' => 'names,emailAddresses,phoneNumbers,birthdays'));
        return $this
            ->contactService->people_connections->listPeopleConnections('people/me',$parameters);
    }

    public function insert($person, $optParams = [])
    {
        return $this->contactService->people->createContact($person, $optParams);
    }


    public function updateContact($resourceName, $person): PeopleService\Person
    {
        $contact = static::get($resourceName);
        $person->etag = $contact->etag;
        $metaData = new PeopleService\PersonMetadata();
        $metaData->setSources($contact->metadata->getSources());
        $person->metadata = $metaData;
        return $this->contactService->people->updateContact($resourceName, $person, array('updatePersonFields' => 'names,emailAddresses,phoneNumbers,birthdays'));
    }

    public function get($id): PeopleService\Person
    {
        return $this->contactService->people->get($id, array('personFields' => 'names,emailAddresses,phoneNumbers,metadata,birthdays'));
    }

    public function deleteContact($resourceName): PeopleService\PeopleEmpty
    {
        return $this->contactService->people->deleteContact($resourceName);
    }
}
