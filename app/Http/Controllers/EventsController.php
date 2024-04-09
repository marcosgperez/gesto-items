<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\EventTypes;
use App\Models\Histories;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{    
    public function index()
    {
        try {
            $events = Events::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($events);
    }

    public function store(Request $request)
    {
        $validated = $this->customValidate($request, [
            'event_type' => 'required|string',
            'start_date' => 'required',
            'description' => 'required|string',
            'observations' => '',
            'photos' => '',
            'history_id' => 'required|integer',
            'item_id' => 'required|integer',
            'location_id' => ''
        ]);
        $payload = auth('api')->getPayload();
        $client_id = $payload->get('client_id');
        $history = Histories::find($validated['history_id']);
        if (empty($history)) {
            return $this->resultError('History not found');
        }
        $item = Items::find($validated['item_id']);
        if (empty($item)) {
            return $this->resultError('Item not found');
        }
        if ($item->client_id != $client_id) {
            return $this->resultError('Item not found');
        }
        $eventTypes = EventTypes::where('client_id', $client_id)->where('name', $validated['event_type'])->get();
        if (empty($eventTypes)) {
            return $this->resultError('Event type not found');
        }
        $event = new Events();
        $event->event_type = $validated['event_type'];
        $event->start_date = $validated['start_date'];
        $event->description = $validated['description'];
        $event->observations = $validated['observations'] ?? ''; 
        $event->photos = $validated['photos'] ?? '';
        $event->history_id = $validated['history_id'];
        $event->item_id = $validated['item_id'];
        try {
            if ($validated['event_type'] == 'Asignacion') {
                $item->location_id = $validated['location_id'];
                $item->save();
            }
            if ($validated['event_type'] == 'Rotura parcial' || $validated['event_type'] == 'Arreglo parcial') {
                $item->status = 2;
                $item->save();
            }
            if ($validated['event_type'] == 'Rotura Total') {
                $item->status = 3;
                $item->save();
            }
            if ($validated['event_type'] == 'Arreglo Total') {
                $item->status = 1;
                $item->save();
            }
            $event->save();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
    }
    
    public function getEvents(Request $request)
    {
        $payload = auth('api')->getPayload();
        $client_id = $payload->get('client_id');
        $data = [];
        $item = Items::find($request->input('id'));
        if (empty($item) || $item->client_id != $client_id) {
            return $this->resultError('Item not found');
        }
        $events = Events::where('history_id', $item->history_id)->get();
        if (empty($events)) {
            return $this->resultError('Events not found');
        }
        $data['item'] = $item;
        $data['events'] = $events;
        return $this->resultOk($data);
    }
}