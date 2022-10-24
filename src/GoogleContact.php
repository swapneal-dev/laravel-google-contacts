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
        $parameters = array_merge($queryParameters, array('personFields' => 'names,emailAddresses,phoneNumbers'));
        return $this
            ->contactService->people_connections->listPeopleConnections('people/me',$parameters);
    }
}
