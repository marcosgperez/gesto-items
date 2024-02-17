<?php

namespace App\Http\Controllers;

use App\Models\Histories;
use App\Models\Sectors;
use App\Models\Items;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index()
    {
        try {
            $items = Items::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($items);
    }

    public function store (Request $request)
    {
        $validated = $this->customValidate($request, [
            'name' => 'required|string',
            'brand' => 'required|string',
            'code' => 'required|string',
            'serial' => 'required|string',
            'model' => 'required|string',
            'chasis' => 'required|string',
            'description' => 'required|string',
            'sector_id' => 'required|integer',
            'manual' => '',
            'photos' => '',
        ]);
        $sector = Sectors::find($validated['sector_id']);
        if (empty($sector)) {
            return $this->resultError('Sector not found');
        }
        $item = new Items();
        $item->name = $validated['name'];
        $item->photos = $validated['photos'] ?? '';
        $item->brand = $validated['brand'];
        $item->code = $validated['code'];
        $item->serial = $validated['serial'];
        $item->model = $validated['model'];
        $item->chasis = $validated['chasis'];
        $item->description = $validated['description'];
        $item->manual = $validated['manual'] ?? '';
        $item->sector_id = $sector->id;
        $item->floor_id = $sector->floor_id;
        try {
            $item->save();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        if ($item->id) {
            $history = new Histories();
            $history->item_id = $item->id;
            try {
                $history->save();
            } catch (\Exception $error) {
                $item->delete();
                return $this->resultError($error->getMessage());
            }
        }
        return $this->resultOk($item);
    }
    
}