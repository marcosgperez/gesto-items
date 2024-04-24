<?php

namespace App\Adapters;

use App\Models\WhatsappPhones;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WhatsappProviderAdapter
{
    public static function new(string $phone)
    {
        $phonesData = WhatsappPhones::getAllPhoneCachedConfigInfo();
        $phoneData = null;
        if (in_array($phone, array_keys($phonesData))) {
            $phoneData = $phonesData[$phone];
        } else {
            throw new NotFoundHttpException("Phone doesn't exists");
        }
        return match (WhatsappServiceProviderEnum::tryFrom('evolution')) {
            WhatsappServiceProviderEnum::evolution => new EvolutionAdapter($phone, $phoneData['token']),
            default => throw new NotFoundHttpException("Service provider doesn't exists")
        };
    }
    public static function fromPhone(string $phone)
    {
        return self::new($phone);
    }
}
