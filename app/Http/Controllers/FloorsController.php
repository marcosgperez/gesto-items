<?php

namespace App\Http\Controllers;

use App\Models\Floors;
use Illuminate\Http\Request;

class FloorsController extends Controller
{
    public function index()
    {
        try {
            $floors = Floors::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($floors);
    }

    public function store(Request $request)
    {
        $validated = $this ->customValidate($request, [
            'name' => 'required|string'
        ]);
        try {
            $floors = Floors::where('name', $validated['name'])->get();
            if(count($floors) > 0) {
                return $this->resultError('Floor already exists');
            }
            $floor = new Floors();
            $floor->name = $validated['name'];
            $floor->save();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($floor);
    }

    public function remove(Request $request)
    {
        $validated = $this ->customValidate($request, [
            'id' => 'required|integer'
        ]);
        try {
            $floor = Floors::find($validated['id']);
            if(empty($floor)) {
                return $this->resultError('Floor not found');
            }
            $floor->delete();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($floor);
    }
}