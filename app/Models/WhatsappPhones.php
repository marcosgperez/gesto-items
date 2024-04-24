<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WhatsappPhones extends Model
{
    protected $fillable = [
        'phone_id',
        'token',
        'phone',
        'phone_owner',
    ];
    public $timestamps = false;

    public static function isValidAlias(string $matchAlias)
    {
        $res = false;
        $phonesData = WhatsappPhones::getAllPhoneCachedConfigInfo();
        foreach ($phonesData  as $alias => $data) {
            if ($alias == $matchAlias) {
                $res = true;
            }
        }
        return $res;
    }
    public static function getAliasFromId(int $matchId)
    {
        $res = "error";
        $phonesData = WhatsappPhones::getAllPhoneCachedConfigInfo();
        foreach ($phonesData as $alias => $data) {
            $id = $data['phone_id'];
            if ($id == $matchId) {
                $res = $alias;
            }
        }
        return $res;
    }
    public static function getIdFromAlias(string $matchAlias)
    {
        $res = 0;
        $phonesData = WhatsappPhones::getAllPhoneCachedConfigInfo();
        foreach ($phonesData as $alias => $data) {
            $id = $data['phone_id'];
            if ($alias == $matchAlias) {
                $res = $id;
            }
        }
        return $res;
    }
    public static function getAllPhoneCachedConfigFromDb()
    {
        $phones = self::all();
        $object = [];
        foreach ($phones as $phone) {
            $object[$phone->phone_id] = [
                'phone_id' => (string) $phone->phone_id,
                "phone" => $phone->phone ?? null,
                "client_id" => $phone->phone_owner,
                "token" => $phone->token,
            ];
        }
        return $object;
    }
    public static function getAllPhoneCachedConfigInfo()
    {

        $config = Cache::rememberForever('WhatsappPhonesConfig', function () {
            return WhatsappPhones::getAllPhoneCachedConfigFromDb();
        });
        return $config;
    }
    public static function getPhoneCachedConfigInfo($clientId)
    {
        $config =  self::getAllPhoneCachedConfigInfo();
        $client_id = self::getAliasFromId($clientId);
        return $config[$client_id];
    }

    public static function cachePhonesConfig()
    {
        $phones = WhatsappPhones::getAllPhoneCachedConfigFromDb();
        return Cache::put('WhatsappPhonesConfig', $phones);
    }
    public static function store($data)
    {
        $phone = new self();
        $phone->phone_id = $data['phone_id'];
        $phone->client_id = $data['client_id'];
        $phone->save();
        self::cachePhonesConfig();
        return $phone;
    }
    public static function create( $client_id,$external_client, $options = [])
    {
        $phone = new WhatsappPhones;
        $phone->client_id = $client_id;     
        $phone->phone_owner = $external_client;
        if(isset($options['token'])){
            $phone->token = $options['token']; 
        }
        if(isset($options['menu_api'])){
            $phone->menu_api = $options['menu_api']; 
        }
        if(isset($options['email_receiver'])){
            $phone->email_receiver = $options['email_receiver']; 
        }
        $phone->save();
        $result = self::cachePhonesConfig();
        if(!$result){
            throw new \Exception("Ocurrió un error al renovar el cache");
        }
        return $phone;
    }
    public static function updatePhone($data)
    {
        $phone = self::where('client_id', $data['client_id'])->first();
        if (!$phone) return null;
        if (!empty($data['phone_id'])) {
            $phone->phone_id = $data['phone_id'];
        }
        if ($phone->isDirty()) {
            $phone->save();
            self::cachePhonesConfig();
        }
        return $phone;
    }
}
