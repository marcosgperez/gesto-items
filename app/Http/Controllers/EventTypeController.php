<?php

namespace App\Http\Controllers;

use App\Models\EventTypes;

class EventTypesController extends Controller
{    
    public function index()
    {
        try {
            $eventTypes = EventTypes::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($eventTypes);
    }
}