<?php

namespace App\Http\Controllers;

use App\Models\EventTypes;

class EventsTypesController extends Controller
{    
    public function index()
    {
        try {
            $payload = auth('api')->getPayload();
            $client_id = $payload->get('client_id');
            $eventTypes = EventTypes::select('id', 'name')->where('client_id', $client_id)->get();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($eventTypes);
    }
}