<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\WhatsappPhones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class WhatsappPhoneController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'phone_id' => 'required',
            'phone_id_alias' => 'required',
            'filters_conversations' => 'boolean',
            'phone_owner' => 'required'
        ]);

        $phone =  WhatsappPhones::store($validated);
        return $this->resultOk($phone);
    }
    public function update(Request $request)
    {
        $validated = $this->validate($request, [
            'phone_id' => '',
            'phone_id_alias' => 'required',
            'filters_conversations' => 'boolean'
        ]);
        $phone =  WhatsappPhones::updatePhone($validated);
        if (!$phone) {
            return response()->json([
                'ok' => 0,
                'status' => 'error',
                'message' => 'phone not found'
            ], 404);
        }
        return $this->resultOk($phone);
    }
    public function updateOwner(Request $request)
    {
        $validated = $this->validate($request, [
            'phone_id_alias' => 'required',
            'phone_owner' => 'required'
        ]);
        $phone =  WhatsappPhones::updateOwner($validated);
        if (!$phone) {
            return response()->json([
                'ok' => 0,
                'status' => 'error',
                'message' => 'phone not found'
            ], 404);
        }
        return $this->resultOk($phone);
    }
    public function index()
    {
        $phones = WhatsappPhones::all();
        return $this->resultOk($phones);
    }

    public function cachePhonesConfig()
    {
        $phones = WhatsappPhones::getAllPhoneCachedConfigFromDb();
        Cache::put('WhatsappPhonesConfig', $phones);
        $config = WhatsappPhones::getAllPhoneCachedConfigInfo();
        return  response()->json([
            'status' => 'ok',
            "phones" =>      $config,
            'message' => 'phones cached'
        ]);
    }
    public function cachePhonesConfigDelete()
    {
        return  response()->json([
            'status' => Cache::delete('WhatsappPhonesConfig'),
            'message' => 'phones cached delete'
        ]);
    }

    public function clearCache()
    {
        Cache::flush();
        return response()->json([
            'msg' => 'cache was deleted successfully ',
        ]);
    }

}