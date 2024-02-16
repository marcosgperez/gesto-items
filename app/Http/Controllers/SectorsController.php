<?php

namespace App\Http\Controllers;

use App\Models\Sectors;
use App\Models\Floors;
use Illuminate\Http\Request;

class SectorsController extends Controller
{
    public function index()
    {
        try {
            $sectors = Sectors::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($sectors);
    }

    public function store(Request $request)
    {
        $validated = $this ->customValidate($request, [
            'name' => 'required|string',
            'floor_id' => 'required|integer'
        ]);
        try {
            $floor = Floors::find($validated['floor_id']);
            if(empty($floor)) {
                return $this->resultError('Floor not found');
            }
            $sectors = Sectors::where('name', $validated['name'])->where('floor_id', $validated['floor_id'])->get();
            if(count($sectors) > 0) {
                return $this->resultError('Sector already exists');
            }
            $sector = new Sectors();
            $sector->name = $validated['name'];
            $sector->floor_id = $validated['floor_id'];
            $sector->save();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($sector);
    }

    public function remove(Request $request)
    {
        $validated = $this ->customValidate($request, [
            'id' => 'required|integer'
        ]);
        try {
            $sector = Sectors::find($validated['id']);
            if(empty($sector)) {
                return $this->resultError('Sector not found');
            }
            $sector->delete();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($sector);
    }
}