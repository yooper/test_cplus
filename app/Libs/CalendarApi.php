<?php

namespace App\Libs;

use App\Models\CalendarEvent;

/**
 * Connect to the calendar API and perform basic functions
 * @author dcardin
 */
class CalendarApi 
{
    /**
     * Used in conjunction with the Auth token
     */
    const BEARER_TOKEN = ' Bearer ';
    
    protected $requestUrlPrefix;
    protected $clientId;
    protected $clientSecret;
    protected $authToken;
    
    public function __construct(string $requestUrlPrefix, string $clientId, string $clientSecret) 
    {
        $this->requestUrlPrefix = $requestUrlPrefix;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getApiUrl() : string
    {
        return sprintf('https://interview.cpdv.ninja/%s/api/', $this->requestUrlPrefix);
    }
    
    public function getAuthToken() : string
    {
        return $this->authToken;
    }
    
    /**
     * Check if authenticated
     * @return bool
     */
    public function isAuthenticated() : bool
    {
        return !empty($this->authToken);
    }
    
    /**
     * Get the auth token
     * @todo Cache the token until its expiration
     */
    public function authenticate()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $this->getApiUrl()."Auth",
                ['json' => ['clientId' => $this->clientId, 'clientSecret' => $this->clientSecret]]);

        $this->authToken = json_decode($response->getBody(), true)['access_token'] ?? null;
    }
    
    public function addEvent(CalendarEvent $calendarEvent) : bool
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $this->getApiUrl()."Events",
                [
                    'json' => $calendarEvent->toArray(),
                    'headers' => ['Authorization' => self::BEARER_TOKEN.$this->getAuthToken()]
                ]);

        return $response->getStatusCode() === 201;        
    }
    
    public function getEvent(CalendarEvent $calendarEvent) : CalendarEvent
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->getApiUrl()."Events/{$calendarEvent->getId()}",
                [
                    'headers' => ['Authorization' => self::BEARER_TOKEN.$this->getAuthToken()]
                ]);
        
        $data = json_decode($response->getBody(), true);
        return new CalendarEvent($data ?? []);        
    }
    
    /**
     * Params are 'int top', 'int skip', 'string filter', 'string orderBy'
     * @param array $params
     * @return array
     */
    public function findEvents(array $params = []) : array
    {
        $client = new \GuzzleHttp\Client();
        $url = $this->getApiUrl()."Events?".http_build_query($params);
        $response = $client->request('GET', $url,
                [
                    'headers' => ['Authorization' => self::BEARER_TOKEN.$this->getAuthToken()]
                ]);

        $data = json_decode($response->getBody(), true) ?? ['total' => 0, 'items' => 0];        
        return  [
            'total' => $data['total'], 
            'results' => array_map(function($item){ return new \App\Models\CalendarEvent($item); }, $data['items'])
        ];
    }   
}
