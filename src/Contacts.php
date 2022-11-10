<?php

namespace SwapnealDev\GoogleContact;

use Google\Exception;
use Google\Service\PeopleService;
use Illuminate\Support\Collection;
use SwapnealDev\GoogleContact\Exceptions\InvalidConfiguration;

/**
 *
 */
class Contacts
{
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

        return collect($googleConnections)->sortBy(function ($person, $index) {
            return $person->names[0]->displayName;
        })->values();
    }

    public static function create(array $fields): PeopleService\Person
    {
        $person = new PeopleService\Person();

        $name = new PeopleService\Name();
        $name->setFamilyName($fields['last_name']);
        $name->setGivenName($fields['first_name']);
        $name->setMiddleName($fields['middle_name'] ?? '');

        $person->setNames([$name]);

        $phoneNumber = new PeopleService\PhoneNumber();
        $phoneNumber->setType('mobile');
        $phoneNumber->setValue($fields['mobile']);

        $phoneNumbers[] = $phoneNumber;

        if (isset($fields['alternate_mobile'])) {
            $alternatePhoneNumber = new PeopleService\PhoneNumber();
            $alternatePhoneNumber->setType('work');
            $alternatePhoneNumber->setValue($fields['alternate_mobile']);
            $phoneNumbers[] = $alternatePhoneNumber;
        }

        $person->setPhoneNumbers($phoneNumbers);

        $email = new PeopleService\EmailAddress();
        $email->setValue($fields['email']);
        $person->setEmailAddresses([$email]);

        if (isset($fields['birthday'])) {
            $birthday = new PeopleService\Birthday();
            $birthday->setText($fields['birthday']);
            $person->setBirthdays([$birthday]);
        }

        $googleContact = static::getGoogleContacts();

        return $googleContact->insert($person);
    }

    public static function update($id, array $fields): PeopleService\Person
    {
        $person = new PeopleService\Person();

        $name = new PeopleService\Name();
        $name->setFamilyName($fields['last_name']);
        $name->setGivenName($fields['first_name']);
        $name->setMiddleName($fields['middle_name'] ?? '');

        $person->setNames([$name]);

        $phoneNumber = new PeopleService\PhoneNumber();
        $phoneNumber->setType('mobile');
        $phoneNumber->setValue($fields['mobile']);

        $phoneNumbers[] = $phoneNumber;

        if (isset($fields['alternate_mobile'])) {
            $alternatePhoneNumber = new PeopleService\PhoneNumber();
            $alternatePhoneNumber->setType('work');
            $alternatePhoneNumber->setValue($fields['alternate_mobile']);
            $phoneNumbers[] = $alternatePhoneNumber;
        }

        $person->setPhoneNumbers($phoneNumbers);

        $email = new PeopleService\EmailAddress();
        $email->setValue($fields['email']);
        $person->setEmailAddresses([$email]);

        if (isset($fields['birthday'])) {
            $birthday = new PeopleService\Birthday();
            $birthday->setText($fields['birthday']);
            $person->setBirthdays([$birthday]);
        }

        $googleContact = static::getGoogleContacts();

        return $googleContact->updateContact($id, $person);
    }

    /**
     * @throws InvalidConfiguration
     * @throws Exception
     */
    public static function delete($id): PeopleService\PeopleEmpty
    {
        $googleContact = static::getGoogleContacts();
        return $googleContact->deleteContact($id);
    }

    public static function find($id): PeopleService\Person
    {
        $googleContact = static::getGoogleContacts();
        return $googleContact->get($id);
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
