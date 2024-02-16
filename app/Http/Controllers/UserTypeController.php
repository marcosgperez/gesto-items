<?php

namespace App\Http\Controllers;

use App\Models\UserTypes;
use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    public function index()
    {
        try {
            $types = UserTypes::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($types);
    }

    public function store(Request $request)
    {
        $validated = $this->customValidate($request, [
            'name' => 'required|string',
        ]);
        try {
            $type = new UserTypes();
            $type->name = $validated['name'];
            $type->save();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($type);
    }

    public function remove(Request $request)
    {
        $validated = $this->customValidate($request, [
            'id' => 'required|integer',
        ]);
        try {
            $type = UserTypes::find($validated['id']);
            $type->delete();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($type);
    }

}