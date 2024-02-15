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
}