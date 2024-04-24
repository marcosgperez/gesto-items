<?php

namespace App\Adapters;
use Illuminate\Support\Facades\Http;

class EvolutionAdapter implements WhatsapAdapterInterface
{

    protected $phoneId;
    private $provider = WhatsappServiceProviderEnum::evolution;
    private $apiKey;
    private $baseUrl = "http://146.190.121.174";

    public function __construct($phoneId, $apiKey)
    {
        $this->phoneId = $phoneId;
        $this->apiKey = $apiKey;
    }
    public function getProvider()
    {
        return $this->provider;
    }
    public  function checkQueue()
    {
    }
    public  function upload($file)
    {
        throw new \Exception("Not implemented");
    }

    public  function contactList()
    {
        $url =   $this->baseUrl . "/chat/findContacts/" . $this->phoneId;
        $res = Http::withHeaders(
            [
                'apiKey' => $this->apiKey
            ]
        )->post($url);
        $json = json_decode($res->getBody()->getContents(), true) ?? [];
        return $json;
    }

    public  function profileImage($conversation)
    {
        return null; //Not implemented
    }

    public  function groupList()
    {
        throw new \Exception("Not implemented");
    }
    public  function checkStatus()
    {
        throw new \Exception("Not implemented");
    }
    public  function deleteMessage($msg_id)
    {
        throw new \Exception("Not implemented");
    }
    public function sendTextMessage($text, $to_number, $reply_to = null, $referenceId = "")
    {
        if (!str_contains($to_number, "@g.us")) {
            $to_number =   $to_number . "@s.whatsapp.net";
        }
        $fields = [
            "number" =>    $to_number,
            "options" =>  [
                "linkPreview" =>  false,
                "presence" =>  "composing",
                "delay" =>  1200
            ],
            "textMessage" =>  [
                "text" =>  $text
            ]
        ];
        $this->addReplyTo($reply_to, $fields);
        return $this->sendMessageRequest($fields, "sendText");
    }
    public function sendReaction()
    {
        //SHOULD BE ADDED TO INTERFACE
    }
    public function sendReplyButton(string $text, array $buttonsData = [], $to_number)
    {
        throw new \Exception("Not implemented");
    }
    public function sendListMessage($list, $to_number)
    {
        throw new \Exception("Not implemented");
    }
    public function sendContactMessage($phone, $to_number, $referenceId = "", $name = null)
    {
        $sendType = "sendContact";
        if (!str_contains($to_number, "@g.us")) {
            $to_number =   $to_number . "@s.whatsapp.net";
        }
        $fields = [
            "number" =>  $to_number,
            "options" =>  [
                "presence" =>  "composing",
                "delay" =>  1200
            ],
            "contactMessage" => [
                [
                    "fullName" => $name,
                    "wuid" =>  $phone,
                    "phoneNumber" => $phone,
                    // "organization"=>  "Company Name", /* Optional */
                    // "email"=> "email", /* Optional */
                    // "url"=>  "url page" /* Optional */
                ]
            ]
        ];
        return $this->sendMessageRequest($fields, $sendType);
    }
    public function sendVCardMessage($vcard, $to_number, $reply_to = null, $referenceId = "")
    {
        throw new \Exception("Not implemented");
    }
    public  function forwardMessage($message, $to_number)
    {
    }
    public function sendMediaMessage($text, $to_number, $reply_to, $link, $original_name, $referenceId = "")
    {
        //MISSING QUOTES
        $path = parse_url($link)['path'];
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $contentType = "";
        if ($extension === "") {
            if (str_starts_with($link, 'data:')) {
                $contentType = substr($link, 0, strpos($link, ";"));
                $contentType = substr($contentType, strpos($contentType, ":") + 1);
            } else {
                $contentType = get_headers($link, 1)["Content-Type"];
            }
        }
        $sendType = "sendMedia";
        if (!str_contains($to_number, "@g.us")) {
            $to_number =   $to_number . "@s.whatsapp.net";
        }
        $fields = [
            "number" =>  $to_number,
            "options" =>  [
                "presence" =>  "composing",
                "delay" =>  1200
            ],
            "mediaMessage" =>  [
                "caption" => $text,
                "media" => $link
            ]
        ];
        $this->addReplyTo($reply_to, $fields);
        if (in_array($extension, ['jpg', 'jpeg', 'gif', 'png', 'svg', 'webp', 'bmp']) || str_starts_with($contentType, 'image')) {
            $file = file_get_contents($link);
            $base64 = base64_encode($file);
            $fields["mediaMessage"]["media"] = $base64;
            $fields["mediaMessage"]["mediatype"] = "image";
        } else if (in_array($extension, ['mp3', 'aac', 'ogg', "opus"]) || str_starts_with($contentType, 'audio')) {
            unset($fields["mediaMessage"]);
            $fields['options'] = [
                "delay" =>  1200,
                "presence" => "recording",
                "encoding" =>  true
            ];
            $file = file_get_contents($link);
            $base64 = base64_encode($file);
            $fields["audioMessage"] =  [
                "audio" => $base64,
            ];
            $sendType = "sendWhatsAppAudio";
        } else if (in_array($extension, ['mp4', '3gp', 'mov']) || str_starts_with($contentType, 'video')) {
            $file = file_get_contents($link);
            $base64 = base64_encode($file);
            $fields["mediaMessage"]["media"] = $base64;

            $fields["mediaMessage"]["mediatype"] = "video";
        } else {
            $file = file_get_contents($link);
            $base64 = base64_encode($file);
            $fields["mediaMessage"]["media"] = $base64;

            $fields["mediaMessage"]["mediatype"] = "document";
            $fields["mediaMessage"]["fileName"] = $original_name;
        }
        return $this->sendMessageRequest($fields, $sendType);
    }
    public function sendLocation($location, $to_number, $referenceId = "")
    {
        if (!str_contains($to_number, "@g.us")) {
            $to_number =   $to_number . "@s.whatsapp.net";
        }
        $fields = [
            "number" =>  $to_number,
            "options" =>  [
                "presence" =>  "composing",
                "delay" =>  1200
            ],
            "locationMessage" =>  [
                "name" => $location['name'] ?? "",
                "address" =>  $location['address'],
                "latitude" =>  $location['lat'],
                "longitude" =>  $location['long']
            ]
        ];
        return $this->sendMessageRequest($fields, "sendLocation");
    }
    // Session Controlling Operations
    //
    public function redeploy()
    {
        throw new \Exception("Not implemented");
    }

    //Session Information Getters
    //
    public function getQr()
    {
        throw new \Exception("Not implemented");
    }
    public function getScreen()
    {
        throw new \Exception("Not implemented");
    }
    public function getStatus()
    {
        throw new \Exception("Not implemented");
    }
    public  function groupInfo($conversation)
    {
        $url =   $this->baseUrl . "/group/findGroupInfos/" . $this->phoneId . "?groupJid=" . $conversation;
        $res = Http::withHeaders(
            [
                'apiKey' => $this->apiKey,
                'accept' => '*/*'
            ]
        )->get($url);
        $json = json_decode($res->getBody()->getContents(), true) ?? null;
        $data = [
            "name" =>  $json["subject"] ?? "Nombre grupo",
            'groupMetadata' => [
                "participants" => $json["participants"] ?? []
            ]
        ];
        return  $data;
    }
    public function getBase64(string $messageId)
    {
        $url =  $this->baseUrl  . "/chat/getBase64FromMediaMessage/" . $this->phoneId;
        $fields = [
            "message" =>  [
                "key" =>  [
                    "id" => $messageId
                ],
            ],
            "convertToMp4" => false

        ];
        return Http::withHeaders(
            [
                'apiKey' => $this->apiKey,
                'Content-type' => 'application/json',
            ]
        )->post($url, $fields);
    }
    public function sendMessageRequest($fields, $messageType)
    {

        $url =   $this->baseUrl . "/message/" . $messageType . "/" . $this->phoneId;
        return Http::withHeaders(
            [
                'apiKey' => $this->apiKey,
                'Content-type' => 'application/json',
            ]
        )->post($url, $fields);
    }
    public function addReplyTo($reply_to, &$fields)
    {
        if ($reply_to) {
            $fields["options"]["quoted"] =  [
                "key" => [
                    "remoteJid" => $reply_to["sender"], //"{{remoteJid}}@s.whatsapp.net",
                    "fromMe" => $reply_to['fromMe'], //true,
                    "id" => $reply_to['id'], //"BAE5B4A2BDFEEFE3",
                ],
                "message" => [
                    "conversation" =>  $reply_to['message'] ?? ""
                ]
            ];
            if ($reply_to['fromMe']) {
                $fields["options"]["quoted"]["key"]["participant"] = $reply_to['participant'];
            }
        }
    }
}
