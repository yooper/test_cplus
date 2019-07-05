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
        $this->assertEquals('demo', $event->getTitle());
        $this->assertEquals('demo', $event->getDescription());
        $this->assertEquals('2019-07-01T09:01:43Z', $event->startDate);
        $this->assertEquals('2019-07-02T09:01:43Z', $event->endDate);               
    }
    
    public function testFindEvents()
    {
        $api = CalenderApiTest::getInstance();
        $api->authenticate();
        $data = $api->findEvents();
        $this->assertEquals(1, $data['total']);    
    }
    
    public function testAddEvent()
    {
        $api = CalenderApiTest::getInstance();
        $api->authenticate();
        $faker = Factory::create();
        $eventData = [
            'id' => null, // id is assigned by the db
            'title' => $faker->name,
            'description' => $faker->sentence,
            'startDate' => $faker->dateTime->format('Y-m-d\TH:i:s\Z'),
            'endDate' => $faker->dateTime->format('Y-m-d\TH:i:s\Z')
        ];

        $successful = $api->addEvent(new CalendarEvent($eventData));
        $this->assertTrue($successful);
        
        $data = $api->findEvents(['filter'=> $eventData['title']]);
        $this->assertGreaterThan(1, $data['total']);
    }    
    
}
