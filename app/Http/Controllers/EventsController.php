<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Models\Histories;
use App\Models\Items;
use Illuminate\Http\Request;

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
            'start_date' => 'required|integer',
            'end_date' => 'required|integer',
            'description' => 'required|string',
            'observations' => '',
            'photos' => '',
            'history_id' => 'required|integer',
            'item_id' => 'required|integer',
        ]);

        $history = Histories::find($validated['history_id']);
        if (empty($history)) {
            return $this->resultError('History not found');
        }
        $item = Items::find($validated['item_id']);
        if (empty($item)) {
            return $this->resultError('Item not found');
        }
        $event = new Events();
        $event->event_type = $validated['event_type'];
        $event->start_date = $validated['start_date'];
        $event->end_date = $validated['end_date'];
        $event->description = $validated['description'];
        $event->observations = $validated['observations'] ?? ''; 
        $event->photos = $validated['photos'] ?? '';
        $event->history_id = $validated['history_id'];
        $event->item_id = $validated['item_id'];
        try {
            $event->save();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
    }
    
    public function getEvents(Request $request)
    {
        $id = $request->input('id');
        $events = Events::where('history_id', $id)->get();
        return $this->resultOk($events);
    }
}