<?php

namespace Tests\Feature;

use App\Libs\CalendarApi;
use App\Models\CalendarEvent;
use Tests\TestCase;
use Faker\Factory;

/**
 * Description of CalenderApiTest
 *
 * @author dcardin
 */
class CalenderApiTest extends TestCase
{       
    
    /**
     * @todo Use mock object to avoid API calls
     * @return CalendarApi
     */
    static protected function getInstance() : CalendarApi
    {
        return new CalendarApi(env('RequestUrlPrefix'), env('ClientId'), env('ClientSecret'));
    }
    
    
    public function testAuthenticate()
    {    
        $api = CalenderApiTest::getInstance();
        $this->assertFalse($api->isAuthenticated());
        $api->authenticate();
        $this->assertTrue($api->isAuthenticated());
    }
    
    public function testGetEvent()
    {
        $api = CalenderApiTest::getInstance();
        $api->authenticate();
        $event = $api->getEvent(new CalendarEvent(['id'=>'6a737fd2-e8aa-499e-8b58-7d774dc8ec64']));
        $this->assertEquals("demo", trim($event->getTitle()));
        $this->assertEquals("demo", trim($event->getDescription()));
        $this->assertEquals('2019-07-01T09:01:43Z', $event->getStart()->format('Y-m-d\TH:i:s\Z'));
        $this->assertEquals('2019-07-02T09:01:43Z', $event->getEnd()->format('Y-m-d\TH:i:s\Z'));               
    }
    
    public function testFindEvents()
    {
        $api = CalenderApiTest::getInstance();
        $api->authenticate();
        $data = $api->findEvents();
        $this->assertGreaterThan(1, $data['total']);    
    }
    
    
    public function testAddEvent()
    {
        $faker = Factory::create();
        $startDate = $faker->dateTimeBetween('+0 days', '+2 weeks');
        $endDate = clone($startDate);
        $endDate->modify("+2 hour");               
        $api = CalenderApiTest::getInstance();
        $api->authenticate();
        $eventData = [
            'id' => null, // id is assigned by the db
            'title' => $faker->name,
            'description' => $faker->sentence,
            'startDate' => $startDate->format('Y-m-d\TH:i:s\Z'),
            'endDate' => $endDate->format('Y-m-d\TH:i:s\Z')
        ];

        $successful = $api->addEvent(new CalendarEvent($eventData));
        $this->assertTrue($successful);
        
        $data = $api->findEvents(['filter'=> $eventData['title']]);
        $this->assertGreaterThan(1, $data['total']);
    } 
  
    
}
