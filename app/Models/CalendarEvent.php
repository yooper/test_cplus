<?php

namespace App\Models;

use DateTime;

/**
 * @author dcardin
 */
class CalendarEvent implements \MaddHatter\LaravelFullcalendar\IdentifiableEvent        
{   
    protected $id;
    protected $title;
    protected $description;
    protected $startDate;
    protected $endDate;
    
    /**
     * Hydrates the internal properties
     * @param array $values
     */
    public function __construct(array $values = []) 
    {
        foreach($values as $key => $value)
        {
            $this->$key = $value;
        }
    }
    
    public function getDescription() : string
    {
        return $this->description;
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }

    public function getEnd(): \DateTime 
    {
        return new DateTime($this->endDate);
    }

    public function getId() 
    {
        return $this->id;
    }

    public function getStart(): \DateTime
    {
        return new DateTime($this->startDate);
    }

    public function getTitle(): string 
    {
        return $this->title;
    }

    public function isAllDay(): bool 
    {
        return false;
    }
    
     /* Optional FullCalendar.io settings for this event
     *
     * @return array
     */
    public function getEventOptions()
    {
        return [
            'description' => $this->description
        ];
    }    

}
