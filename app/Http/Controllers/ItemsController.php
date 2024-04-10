<?php

namespace App\Http\Controllers;

use App\Models\FifoMessages;
use App\Models\Histories;
use App\Models\Sectors;
use App\Models\Items;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\Events;
class ItemsController extends Controller
{

    private function _generateAndUploadQr($itemId)
    {
        $frontUrl = "$itemId";
        $fileName = "qr-code-$itemId.png";
        $qrCode = QrCode::size(200)->format('png')->generate($frontUrl);
        Storage::disk('s3')->put($fileName, $qrCode);
        $url = 'https://gesto-items.s3.amazonaws.com/' . $fileName;
        return $url;
    }

    public function index()
    {
        try {
            $items = Items::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($items);
    }

    public function store(Request $request)
    {
        $validated = $this->customValidate($request, [
            'name' => 'required|string',
            'brand' => 'string',
            'code' => 'string',
            'serial' => 'string',
            'model' => 'string',
            'chasis' => 'string',
            'description' => 'string',
            'sector_id' => 'integer',
            'manual' => '',
            'photos' => '',
            'location_id' => '',
            'image' => ''
        ]);

        if (!empty($validated['sector_id'])) {
            $sector = Sectors::find($validated['sector_id']);
            if (empty($sector)) {
                return $this->resultError('Sector not found');
            }
        }
        $payload = auth('api')->getPayload();
        $client_id = $payload->get('client_id');

        if (!empty($validated['image'])) {
            $file = $request->file('image');
            $path = $file->store('images/' . $client_id . '/items', 's3');
            $imageUrl = Storage::disk('s3')->url($path);
        }

        $item = new Items();
        $item->name = $validated['name'];
        $item->photos = $validated['photos'] ?? '';
        $item->brand = $validated['brand'] ?? '';
        $item->code = $validated['code'] ?? '';
        $item->serial = $validated['serial'] ?? '';
        $item->model = $validated['model'] ?? '';
        $item->chasis = $validated['chasis'] ?? '';
        $item->description = $validated['description'] ?? '';
        $item->manual = $validated['manual'] ?? '';
        $item->location_id = $validated['location_id'] ?? null;
        $item->sector_id = $sector->id ?? null;
        $item->floor_id = $sector->floor_id ?? null;
        $item->image_url = $imageUrl ?? '';
        $item->client_id = $client_id;
        try {
            $item->save();
            $qr = $this->_generateAndUploadQr($item->id);
            $item->qr = $qr;
            $item->save();
            $history = new Histories();
            $history->item_id = $item->id;
            $history->save();
            $item->history_id = $history->id;
            $item->save();
            return $this->resultOk($item);
        } catch (\Exception $error) {
            // En caso de error, devolver un error
            return $this->resultError($error->getMessage());
        }
    }

    public function remind_status()
    {
        $data = [];
        $items = Items::where('status', '!=', 1)->get();
        $currentDate = date('Y-m-d');
        foreach ($items as $item) {
            $lastReminder = $item->last_reminder;
            $interval = $item->reminder_interval;
            $diffInSeconds = strtotime($currentDate) - strtotime($lastReminder);
            $diffInDays = $diffInSeconds / 86400;
            if ($diffInDays >= $interval) {
                try {
                    $msg = FifoMessages::create([
                        'instance' => 'codeUp',
                        'phone' => $item->phones_to_remind,
                        'message' => $item->text_to_send
                    ]);
                } catch (\Exception $error) {
                    $msg = $error->getMessage();
                }
                $data[] = $msg;
                $item->last_reminder = $currentDate;
                $item->save();
            }
        }
        return $this->resultOk($data);
    }

    public function set_reminder(Request $request)
    {
        $validated = $this->customValidate($request, [
            'id' => 'required|integer',
            'status' => 'required|integer',
            'reminder_interval' => 'required|integer',
            'text_to_send' => 'required|string',
            'phones_to_remind' => 'required|string'
        ]);

        $item = Items::where('id', $validated['id'])->first();
        if (empty($item)) {
            return $this->resultError('Item not found');
        }
        $item->status = $validated['status'];
        $item->reminder_interval = $validated['reminder_interval'];
        $item->text_to_send = $validated['text_to_send'];
        $item->phones_to_remind = $validated['phones_to_remind'];
        try {
            $item->save();
            return $this->resultOk($item);
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
    }

    public function greenItem(Request $request)
    {
        $validated = $this->customValidate($request, [
            'id' => 'required|integer'
        ]);
        $item = Items::where('id', $validated['id'])->first();
        if (empty($item)) {
            return $this->resultError('Item not found');
        }
        $item->status = 1;
        $item->last_reminder = null;
        $item->reminder_interval = null;
        $item->text_to_send = null;
        $item->phones_to_remind = null;
        try {
            $item->save();
            return $this->resultOk($item);
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
    }

    public function search(Request $request)
    {
        $validated = $this->customValidate($request, [
            'query' => 'nullable|string',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ]);
    
        $query = $validated['query'];
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
    
        try {
            $payload = auth('api')->getPayload();
            $client_id = $payload->get('client_id');
    
            $itemsQuery = Items::where('client_id', $client_id);
    
            if ($query !== null) {
                $itemsQuery->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('code', 'like', "%$query%")
                        ->orWhere('brand', 'like', "%$query%")
                        ->orWhere('serial', 'like', "%$query%")
                        ->orWhere('name', 'like', "%$query%")
                        ->orWhere('model', 'like', "%$query%");
                });
            }
    
            $paginatedItems = $itemsQuery->paginate($perPage, ['*'], 'page', $page);
    
            return response([
                'ok' => 1,
                'data' => $paginatedItems->items(),
                'current_page' => $paginatedItems->currentPage(),
                'from' => $paginatedItems->firstItem(),
                'last_page' => $paginatedItems->lastPage(),
                'per_page' => $paginatedItems->perPage(),
                'to' => $paginatedItems->lastItem(),
                'total' => $paginatedItems->total()
            ], 200);
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
    }    

    public function getItemDetails(Request $request) 
    {
        $validated = $this->customValidate($request, [
            'id' => 'required|integer'
        ]);
        $data = [];
        $payload = auth('api')->getPayload();
        $client_id = $payload->get('client_id');
        $item = Items::where('id', $validated['id'])->where('client_id', $client_id)->first();
        if (empty($item)) {
            return $this->resultError('Item not found');
        }
        $events = Events::where('history_id', $item->history_id)->get();
        $data['item'] = $item;
        $data['events'] = empty($events) ? null : $events;
        return $this->resultOk($data);
    }

}