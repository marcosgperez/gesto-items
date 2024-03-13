<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Clients;
class UsersController extends Controller
{
    const COLORS = [
        "#FFCC80",
        "#FFAB91",
        "#FFF176",
        "#B39DDB",
        "#80CBC4",
        "#90CAF9"
    ];
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

    public function store(Request $request)
    {
        $validated = $this->customValidate($request, [
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'user_type_id' => 'required|integer',
            'client_id' => 'required|integer'
        ]);
        try {
            $client = Clients::find($validated['client_id']);
            if (empty($client)) {
                return $this->resultError('Client not found');
            }
            $user = new User();
            $user->name = $validated['name'];
            $user->surname = $validated['surname'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->client_id = $validated['client_id'];
            $userTypes = UserTypes::find($validated['user_type_id']);
            if (empty($userTypes)) {
                return $this->resultError('User type not found');
            }
            $user->user_type_id = $validated['user_type_id'];
            $initials = strtoupper(substr($validated['name'], 0, 1) . substr($validated['surname'], 0, 1));
            $user->profile_initials = $initials;
            $user->profile_color = self::COLORS[array_rand(self::COLORS)];
            $user->save();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($user);
    }

    public function remove(Request $request)
    {
        $validated = $this->customValidate($request, [
            'id' => 'required|integer',
        ]);
        try {
            $user = User::find($validated['id']);
            if (empty($user)) {
                return $this->resultError('User not found');
            }
            $user->delete();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($user);
    }
}