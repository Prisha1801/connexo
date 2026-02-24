<?php

namespace App\Helpers;

use HubSpot\Factory;
use HubSpot\Client\Crm\Contacts\ApiException;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput;
use HubSpot\Client\Crm\Contacts\Model\BatchInputSimplePublicObjectBatchInput;
use HubSpot\Client\Crm\Lists\Api\ListsApi;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectId;
use HubSpot\Client\Crm\Contacts\Model\BatchInputSimplePublicObjectId;
use HubSpot\Client\Crm\Contacts\Model\Filter;
use HubSpot\Client\Crm\Contacts\Model\FilterGroup;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest;

class HubSpotHelper
{
    /* Brij Mohan Negi */
    protected $hubspot;

    public function __construct()
    {
        // Fetch the API key from env
        //added by amit pawar 18-11-2025
        $apiKey = env('HUBSPOT_API_KEY');

         if (empty($apiKey) || env('APP_ENV') === 'local') {
            $this->hubspot = null;  // prevent SDK from executing
            return;
        }
        // Fetch the Access token from env
       //ended by amit pawar 18-11-2025
        $this->hubspot = Factory::createWithAccessToken($apiKey);
    }

    protected static $hubSpotClient;

    public static function getHubSpotClient()
    {
        if (is_null(self::$hubSpotClient)) {
            self::$hubSpotClient = Factory::create();
        }
        return self::$hubSpotClient;
    }

    // Function to add a contact
    public function addContact($contactData)
    {
        $contactInput = new SimplePublicObjectInput(['properties' => $contactData]);

        try {
            $response = $this->hubspot->crm()->contacts()->basicApi()->create($contactInput);
            return $response;
        } catch (ApiException $e) {
            return $e->getMessage();
        }
    }

    // Function to update a contact
    public function updateContact($contactId, $contactData)
    {
        $contactInput = new SimplePublicObjectInput(['properties' => $contactData]);

        try {
            $response = $this->hubspot->crm()->contacts()->basicApi()->update($contactId, $contactInput);
            return $response;
        } catch (ApiException $e) {
            return $e->getMessage();
        }
    }

    // Function to add a contact to a list
    public function addContactToList($listId, $contactId)
    {
        try {
            // 
            //   $requestBody = [$contactId];

            $this->hubspot->crm()->lists()->membershipsApi()->add($listId, [$contactId]);
            return response()->json(['message' => 'Contact added to list successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function findContactIdByEmail($email)
    {
        // Set up the filter for searching by email
        $filter = new Filter([
            'propertyName' => 'email',
            'operator' => 'EQ',
            'value' => $email,
        ]);

        $filterGroup = new FilterGroup(['filters' => [$filter]]);
        $searchRequest = new PublicObjectSearchRequest([
            'filterGroups' => [$filterGroup],
            'properties' => ['email'], // You can add more properties if needed
            'limit' => 1, // Limit to one result to get the exact contact
        ]);

        try {
            // Perform the search
            $response = $this->hubspot->crm()->contacts()->searchApi()->doSearch($searchRequest);

            if (count($response->getResults()) > 0) {
                $contact = $response->getResults()[0];
                $contactId = $contact->getId();

                return response()->json([
                    'message' => 'Contact found',
                    'contact_id' => $contactId,
                ], 200);
            } else {
                return response()->json(['message' => 'Contact not found'], 404);
            }
        } catch (ApiException $e) {
            //\Log::error('HubSpot Search Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to find contact',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Function to remove a contact from a list
    public function removeContactFromList($listId, $contactId)
    {
        try {
            // $requestBody = [$contactId];

            $this->hubspot->crm()->lists()->membershipsApi()->remove($listId, [$contactId]);
            return "Contact removed from the list successfully.";
        } catch (ApiException $e) {
            return $e->getMessage();
        }
    }
}
