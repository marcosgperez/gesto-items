<?php

namespace App\Http\Controllers;

use App\Models\EventTypes;

class EventsTypesController extends Controller
{    
    public function index()
    {
        try {
            $eventTypes = EventTypes::select('id', 'name')->get();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($eventTypes);
    }
}