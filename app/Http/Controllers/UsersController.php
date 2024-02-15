<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($users);
    }
    public function indexById(Request $request)
    {
        $validated = $this->customValidate($request, [
            'id' => 'required|integer',
        ]);
        try {
            $user = User::find($validated['id']);
            if (empty($user)) {
                return $this->resultError('User not found');
            }
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($user);
    }
}