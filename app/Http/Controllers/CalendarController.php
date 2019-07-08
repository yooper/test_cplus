<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Response;
use App\Libs\CalendarApi;
use App\Models\CalendarEvent;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\ClientException;


/**
 * 
 * Handle incoming requests for interacting with the calender
 *
 * @author dcardin
 */
class CalendarController extends Controller
{             
    /**
     * Creates and initiates the api authentication
     * @return CalendarApi
     */
    protected function getApiInstance() : CalendarApi
    {
        $api = new CalendarApi(env('RequestUrlPrefix'), env('ClientId'), env('ClientSecret'));
        $api->authenticate();
        return $api;
    }    
    
    public function index(Request $request)
    {              
        $api = $this->getApiInstance();
        $events = [];
        try
        {
            $events = $api->findEvents()['results'];  
        } 
        catch (ClientException $e)
        {
            $events = [];
        }
        
        // filter events by keyword, not implemented
        if($request->has('filter'))
        {
            $filterOn = strtolower($request->get('filter'));        
            $events = array_filter($events, function($event){
                
                Str::contains(strtolower( $event->title ), $filterOn) || Str::contains(strtolower($event->description), $filterOn);                 
            });
        }
        
        $calendar = \Calendar::addEvents($events);
        $calendar->setId('civicPlusCalendar');        
        $calendar->setCallbacks([
            'eventClick' => 'function(event) { eventDialog(event)}',
            'dayClick' => 'function(date, jsEvent, view){ openDialog(date); }'
        ]);        
        
        return view('events', compact('calendar'));           
    }
    
    public function addEvent(Request $request)
    {
        $validate = [
           'startDate' => 'required',
           'endDate' => 'required',
           'title' => 'required',
           'description' => 'required'                        
        ];
        
        $validator = Validator::make($request->all(), $validate);
        
        // Validate the input and return correct response
        if ($validator->fails())
        {
            return Response::json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ], 400); // 400 being the HTTP code for an invalid request.
        } 

        $api = $this->getApiInstance();
        $newEvent = new CalendarEvent([
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),         
        ]);
        
        if($api->addEvent($newEvent))
        {
            return Response::json(['success' => true], 200);  
        }        
        return Response::json(['success' => false, 'errors'=>['General Error' => ['API could not save the event']] ], 200);  
    }
    
    public function get(Request $request)
    {       
        $api = $this->getApiInstance();
        $validator = Validator::make($request->all(),[
           'id' => 'required'                       
        ]);
        
        $calendarEvent = $api->getEvent(new CalendarEvent(['id' => $request->get('id')]));
        
        // Validate the input and return correct response
        if ($validator->fails())
        {
            return Response::json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ], 400); // 400 being the HTTP code for an invalid request.
        } 
        elseif($calendarEvent === null)
        {
            return Response::json(array(
                'success' => false,
                'errors' => ['id' => 'Event not found']

            ), 401);           
        }
        return Response::json(['success' => true, 'event' => $calendarEvent->toArray() ], 200);                 
    }            
}
