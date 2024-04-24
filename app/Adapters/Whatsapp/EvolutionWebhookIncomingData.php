<?php

namespace App\Adapters\Whatsapp;

use App\Adapters\EvolutionAdapter;
use App\Adapters\WhatsappServiceProviderEnum;
use App\Models\WhatsappPhones;

class EvolutionWebhookIncomingData implements WebhookIncomingData
{
    private $provider = WhatsappServiceProviderEnum::evolution;

    private $_originalObject;
    private $_messageType;
    private $_messageMimeType;
    private $_messageId;
    private $_clientId;
    private $_conversation;
    private $_userName;
    private $_messageText;
    private $_mediaUrl;
    private $_userId;
    private $_timestamp;
    private $_receiver;
    private $_sender;
    private $_quotedMessageId = null;
    private $_quotedMessageParticipant = null;
    private $_quotedMessageText;
    private $_quotedMessageMediaUrl;
    private $_quotedMessageType;
    private $_quotedMessageMimeType;
    private $_quotedMessagevCardList;
    private $_isForwarded = false;
    private $_location = null;
    private $_vCardList = null;
    private $_filename;
    private $_fromMe;
    public function __construct($data)
    {
        ///
        $this->_originalObject = $data;
        switch ($data['data']['messageType']) {
                //create enum, check ultra types
            case "extendedTextMessage":
                $this->_messageType = "text";
                $this->_messageText = $data['data']['message']['extendedTextMessage']['text'];
                $this->addQuoted($data['data']['message']['extendedTextMessage']);
                $this->addIsForwarded($data['data']['message']['extendedTextMessage']);
                $this->_mediaUrl = null;
                break;
            case "conversation":
                $this->_messageType = "text";
                $this->_messageText = $data['data']['message']['conversation'];
                $this->_mediaUrl = null;
                break;
            case "imageMessage":
                $this->_messageType = "image";
                $this->_messageText = $data['data']['message']['imageMessage']['caption'] ?? "";
                if (isset($data['data']['message']['base64'])) {
                    $this->_mediaUrl = "data:" . $data['data']['message']['imageMessage']['mimetype'] . ";base64," . $data['data']['message']['base64'];
                } else {
                    $this->_mediaUrl = $data['data']['message']['imageMessage']['url'];
                }
                $this->_messageMimeType  = $data['data']['message']['imageMessage']['mimetype'];
                $this->addQuoted($data['data']['message']['imageMessage']);
                $this->addIsForwarded($data['data']['message']['imageMessage']);
                break;
            case "stickerMessage":
                $this->_messageType = "image";
                $this->_messageText = "";
                $this->_mediaUrl = $data['data']['message']['stickerMessage']['url'];
                $this->_messageMimeType  = $data['data']['message']['stickerMessage']['mimetype'];
                $this->addQuoted($data['data']['message']['stickerMessage']);
                $this->addIsForwarded($data['data']['message']['stickerMessage']);
                break;
            case "videoMessage":
                $this->_messageType = "video";
                $this->_messageText = $data['data']['message']['videoMessage']['caption'] ?? "";
                $this->_mediaUrl = $data['data']['message']['videoMessage']['url'];
                $this->_messageMimeType  = $data['data']['message']['videoMessage']['mimetype'];
                $this->addQuoted($data['data']['message']['videoMessage']);
                $this->addIsForwarded($data['data']['message']['videoMessage']);
                break;
            case "documentMessage":
                $this->_messageType = "document";
                $this->_messageText = "";
                $this->_mediaUrl = $data['data']['message']['documentMessage']['url'];
                $this->_messageMimeType  = $data['data']['message']['documentMessage']['mimetype'];
                $this->_filename = $data['data']['message']['documentMessage']['title'] ?? '';
                $this->addQuoted($data['data']['message']['documentMessage']);
                $this->addIsForwarded($data['data']['message']['documentMessage']);
                break;
            case "documentWithCaptionMessage":
                $this->_messageType = "document";
                $this->_messageText = $data['data']['message']['documentWithCaptionMessage']['message']['documentMessage']['caption'];
                $this->_mediaUrl = $data['data']['message']['documentWithCaptionMessage']['message']['documentMessage']['url'];
                $this->_messageMimeType  = $data['data']['message']['documentWithCaptionMessage']['message']['documentMessage']['mimetype'];
                $this->_filename =  $data['data']['message']['documentWithCaptionMessage']['message']['documentMessage']['title'] ?? '';
                $this->addQuoted($data['data']['message']['documentWithCaptionMessage']['message']['documentMessage']);
                $this->addIsForwarded($data['data']['message']['documentWithCaptionMessage']['message']['documentMessage']);
                break;
            case "audioMessage":
                $this->_messageType = "audio";
                $this->_messageText = "";
                $this->_mediaUrl = $data['data']['message']['audioMessage']['url'];
                $this->_messageMimeType  = $data['data']['message']['audioMessage']['mimetype'];
                $this->addQuoted($data['data']['message']['audioMessage']);
                $this->addIsForwarded($data['data']['message']['audioMessage']);
                break;
            case "contactMessage":
                $this->_messageType = "vcard";
                $this->_messageText = "";
                $this->_mediaUrl = null;
                $this->_vCardList = [
                    [
                        "vcard" =>  $data['data']['message']['contactMessage']['vcard']
                    ]
                ];
                $this->addQuoted($data['data']['message']['contactMessage']);
                $this->addIsForwarded($data['data']['message']['contactMessage']);
                break;
            case "contactsArrayMessage":
                $this->_messageType = "vcard";
                $this->_messageText = "";
                $this->_mediaUrl = null;
                $this->_vCardList = [];
                foreach ($data['data']['message']['contactsArrayMessage']['contacts'] as $vcard) {
                    $this->_vCardList[] = [
                        "vcard" => $vcard['vcard']
                    ];
                }
                $this->addQuoted($data['data']['message']['contactsArrayMessage']);
                $this->addIsForwarded($data['data']['message']['contactsArrayMessage']);

                break;
            case "locationMessage":
                $this->_messageType = "location";
                $this->_messageText = "";
                $this->_mediaUrl = null;
                $this->_location = $data['data']['message']['locationMessage']['degreesLatitude'] . "," . $data['data']['message']['locationMessage']['degreesLongitude'];
                $this->addQuoted($data['data']['message']['locationMessage']);
                $this->addIsForwarded($data['data']['message']['locationMessage']);
                break;
            case "liveLocationMessage":
                $this->_messageType = "location";
                $this->_messageText = "";
                $this->_mediaUrl = null;
                $this->_location = $data['data']['message']['liveLocationMessage']['degreesLatitude'] . "," . $data['data']['message']['liveLocationMessage']['degreesLongitude'];
                $this->addQuoted($data['data']['message']['liveLocationMessage']);
                $this->addIsForwarded($data['data']['message']['liveLocationMessage']);
                break;
            default:
                $this->_messageType = "error";
        }
        $this->_clientId = $data['instance'];


        $this->_fromMe = $data['data']['key']['fromMe'];


        // groups missing @g.us
        if (strpos($data['data']['key']['remoteJid'], "@g.us") !== false) {
            $this->_conversation =   $data['data']['key']['remoteJid']; // $data['data']['key']['remoteJid']; //  "5491140442120@s.whatsapp.net"
            $this->_userId = explode("@", $data['data']['key']['participant'])[0] . "@c.us"; // $data['data']['key']['remoteJid']; //  "5491140442120@s.whatsapp.net"
            $this->_sender = $data['data']['key']['participant']; // "5491168297299@s.whatsapp.net"
        } else {
            $this->_conversation =  explode("@", $data['data']['key']['remoteJid'])[0] . "@c.us"; // $data['data']['key']['remoteJid']; //  "5491140442120@s.whatsapp.net"
            $this->_userId = explode("@", $data['data']['key']['remoteJid'])[0] . "@c.us"; // $data['data']['key']['remoteJid']; //  "5491140442120@s.whatsapp.net"
            $this->_sender = $data['data']['key']['remoteJid']; // "5491168297299@s.whatsapp.net"
        }
        $this->_receiver =  explode("@", $data['sender'])[0] . "@c.us"; //$data['sender']; // "5491168297299@s.whatsapp.net"
        $this->_userName = $data['data']['pushName']; // CHECK NULL

        $this->_timestamp = $data['data']['messageTimestamp'];
        $this->_messageId = $data['data']['key']['id'];
    }
    public function getFilename()
    {
        return $this->_filename;
    }
    public function originalObject()
    {
        return $this->_originalObject;
    }
    public function phoneId()
    {
        return $this->_clientId;
    }
    public function messageType()
    {
        return $this->_messageType;
    }
    public function messageText()
    {
        return $this->_messageText;
    }
    public function setMessageText($text)
    {
        $this->_messageText = $text;
    }
    public function conversation()
    {
        return $this->_conversation;
    }
    public function conversation_name()
    {
        return $this->_userName;
    }
    public function mediaUrl()
    {

        return $this->_mediaUrl;
    }
    public function messageTextOrCaption()
    {
        return $this->messageText();
    }

    public function userName()
    {

        return $this->_userName;
    }
    public function userId()
    {
        return $this->_userId;
    }
    public function userPhone()
    {
        return substr($this->_userId, 0, 13);
    }
    public function userImage()
    {
        return null;
    }
    public function timestamp()
    {
        return $this->_timestamp;
    }
    public function messageId()
    {
        return $this->_messageId;
    }
    public function messageSubtype()
    {
        dd("NO IMPLEMENTADO");

        return null;
    }
    public function messageParticipant()
    {
        throw new \Exception("Not implemented");
        return null;
    }
    public function receiver()
    {
        return $this->_receiver;
    }
    public function isFromMe()
    {
        return $this->_fromMe;
    }
    public function mediaMimeType()
    {
        return $this->_messageMimeType ?? null;
    }
    public function quotedMessageId()
    {
        return $this->_quotedMessageId;
    }
    public function quotedMessageText()
    {

        return $this->_quotedMessageText;
    }
    public function quotedMessageMediaUrl()
    {

        return $this->_quotedMessageMediaUrl;
    }
    public function quotedMessageType()
    {
        return $this->_quotedMessageType;
    }
    public function quotedMessagevCardList()
    {

        return $this->_quotedMessagevCardList;
    }
    public function isForwarded()
    {
        return $this->_isForwarded;
    }
    // public function skipBotCheckAndSendMessage()
    // {
    //     throw new \Exception("Not implemented");
    // }
    public function participants()
    {
        return null;
    }
    public function location()
    {
        return $this->_location;
    }
    public function vCardList()
    {
        return $this->_vCardList;
    }
    public function interactive_reply_id()
    {
        return null;
    }
    public function composeQuotedMessage()
    {

        if (!$this->quotedMessageId()) {
            return null;
        }
        $qm = [
            "text" => $this->quotedMessageText(),
            "id" => $this->quotedMessageId(),
            "type" => "text"
        ];
        if ($this->quotedMessageMediaUrl()) {
            $qm['url'] = $this->quotedMessageMediaUrl();
            $qm['type'] = $this->quotedMessageType();
        }
        if ($this->quotedMessagevCardList()) {
            $qm['vcardList'] = $this->quotedMessagevCardList();
            $qm['type'] = $this->quotedMessageType();
            $qm['text'] = "";
        }
        return $qm;
    }
    public function sender()
    {
        return $this->_sender;
    }
    public function composeMessage()
    {
        $message = [
            'type' => $this->messageType(),
            'url' => $this->mediaUrl(),
            'text' => $this->messageText(),
            "id" => $this->messageId(),
            "vcardList" => null,
            "contact" => null,
            "buttons" => null,
            "list" => null,
            "sender" => $this->sender(),
        ];

        if ($this->getFilename()) {
            $message['filename'] = $this->getFilename();
        }
        if ($this->isForwarded()) {
            $message['isForwarded'] = true;
        }
        if ($this->location()) {
            $message['payload'] = $this->location();
        }
        if ($this->vCardList()) {
            $message['vcardList'] = $this->vCardList();
        }

        return $message;
    }
    public function composeUser()
    {
        return [

            "id" => $this->userId(),
            "name" => $this->userName(),
            "phone" => $this->userPhone(),


        ];
    }
    public function serviceProvider()
    {
        return $this->provider;
    }

    //ack
    public function ackStatus()
    {
        return null;
    }

    public function isTest()
    {
        return false;
    }

    public function retrieveQuotedMediaUrl()
    {
        if ($this->_quotedMessageMediaUrl && !str_starts_with($this->_quotedMessageMediaUrl, "data:image")) {
            $phonesData = WhatsappPhones::getAllPhoneCachedConfigInfo();
            $phoneData = null;
            if (in_array($this->_clientId, array_keys($phonesData))) {
                $phoneData = $phonesData[$this->_clientId];
            }
            $evolution = new EvolutionAdapter($this->_clientId, $phoneData['token']);
            $base64 = $evolution->getBase64($this->_quotedMessageId);
            if ($base64->status() === 200 || $base64->status() === 201) {
                $base64 = $base64->json();
                $mime = $base64['mimetype'];
                $base64 = $base64['base64'];
                $this->_quotedMessageMediaUrl = "data:" . $mime . ";base64," . $base64;
            }
        }
    }
    public function addQuoted($message)
    {
        if (isset($message['contextInfo']['quotedMessage'])) {
            $contextInfo = $message['contextInfo'];
            $this->_quotedMessageId = $contextInfo['stanzaId'];
            $this->_quotedMessageParticipant = $contextInfo['participant'];

            if (isset($contextInfo['quotedMessage']['conversation'])) {
                $this->_quotedMessageText = $contextInfo['quotedMessage']['conversation'];
                $this->_quotedMessageMediaUrl = null;
                $this->_quotedMessageType = "text";
                $this->_quotedMessagevCardList = null;
            } else if (isset($contextInfo['quotedMessage']['extendedTextMessage'])) {
                $this->_quotedMessageText = $contextInfo['quotedMessage']['extendedTextMessage']['text'];
                $this->_quotedMessageMediaUrl = null;
                $this->_quotedMessageType = "text";
                $this->_quotedMessagevCardList = null;
            } else if (isset($contextInfo['quotedMessage']['imageMessage'])) {
                $this->_quotedMessageType = "image";
                $this->_quotedMessageText = $contextInfo['quotedMessage']['imageMessage']['caption'] ?? "";
                $this->_quotedMessageMediaUrl = "data:image/jpeg;base64," . $contextInfo['quotedMessage']['imageMessage']['jpegThumbnail'];
            } else if (isset($contextInfo['quotedMessage']['audioMessage'])) {
                $this->_quotedMessageType = "audio";
                $this->_quotedMessageText = "";
                $this->_quotedMessageMediaUrl = $contextInfo['quotedMessage']['audioMessage']['url'];
            } else if (isset($contextInfo['quotedMessage']['documentMessage'])) {
                $this->_quotedMessageType = "document";
                $this->_quotedMessageText = $contextInfo['quotedMessage']['documentMessage']['caption'] ?? "";
                $this->_quotedMessageMediaUrl = "data:image/jpeg;base64," . $contextInfo['quotedMessage']['documentMessage']['jpegThumbnail'];
            } else if (isset($contextInfo['quotedMessage']['stickerMessage'])) {
                $this->_quotedMessageType = "image";
                $this->_quotedMessageText =  "sticker";
                $this->_quotedMessageMediaUrl =  $contextInfo['quotedMessage']['stickerMessage']['url'];
            } else if (isset($contextInfo['quotedMessage']['videoMessage'])) {
                $this->_quotedMessageType = "video";
                $this->_quotedMessageText = $contextInfo['quotedMessage']['videoMessage']['caption'] ?? "";
                $this->_quotedMessageMediaUrl = "data:image/jpeg;base64," . $contextInfo['quotedMessage']['videoMessage']['jpegThumbnail'];
            } else if (isset($contextInfo['quotedMessage']['documentWithCaptionMessage'])) {
                $this->_quotedMessageType = "document";
                $this->_quotedMessageText = $contextInfo['quotedMessage']['documentWithCaptionMessage']['message']['documentMessage']['caption'] ?? "";
                $thumb = $contextInfo['quotedMessage']['documentWithCaptionMessage']['message']['documentMessage']['jpegThumbnail'] ?? null;
                if ($thumb) {
                    $this->_quotedMessageMediaUrl = "data:image/jpeg;base64," . $thumb;
                } else {
                    $this->_quotedMessageMediaUrl = null;
                }
            } else if (isset($contextInfo['quotedMessage']['contactMessage'])) {
                $this->_quotedMessageType = "vcard";
                $this->_quotedMessageText = "";
                $this->_quotedMessagevCardList = [
                    [
                        "vcard" => $contextInfo['quotedMessage']['contactMessage']['vcard']
                    ]
                ];
            } else if (isset($contextInfo['quotedMessage']['contactsArrayMessage'])) {
                $this->_quotedMessageType = "vcard";
                $this->_quotedMessageText = "";
                $this->_quotedMessagevCardList = [];
                foreach ($contextInfo['quotedMessage']['contactsArrayMessage']['contacts'] as $conatct) {
                    $this->_quotedMessagevCardList[] = ["vcard" => $conatct['vcard']];
                }
            } else if (isset($contextInfo['quotedMessage']['locationMessage'])) {
                $this->_quotedMessageType = "location";
                $this->_quotedMessageText = $contextInfo['quotedMessage']['locationMessage']['caption'] ?? "";
                $this->_quotedMessageMediaUrl = "data:image/jpeg;base64," . $contextInfo['quotedMessage']['locationMessage']['jpegThumbnail'];
            } else if (isset($contextInfo['quotedMessage']['liveLocationMessage'])) {
                $this->_quotedMessageType = "location";
                $this->_quotedMessageText = $contextInfo['quotedMessage']['liveLocationMessage']['caption'] ?? "";
                $this->_quotedMessageMediaUrl = "data:image/jpeg;base64," . $contextInfo['quotedMessage']['liveLocationMessage']['jpegThumbnail'];
            }
        }
    }
    public function addIsForwarded($message)
    {
        if (isset($message['contextInfo']['isForwarded']) && $message['contextInfo']['isForwarded'] === true) {
            $this->_isForwarded = true;
        }
    }
}
