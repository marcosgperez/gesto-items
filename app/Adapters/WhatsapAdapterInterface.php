<?php

namespace App\Adapters;


interface WhatsapAdapterInterface
{
    public  function checkQueue();
    public  function upload($file);

    public  function contactList();
    public  function groupInfo($conversation);
    public  function profileImage($conversation);

    public  function groupList();
    public  function checkStatus();
    public  function deleteMessage($msg_id);
    public function sendTextMessage($text, $to_number, $reply_to = null, $referenceId = "");
    public function sendContactMessage($phone, $to_number, $referenceId = "", $name = null);
    public function sendVCardMessage($vcard, $to_number, $reply_to = null, $referenceId = "");
    public  function forwardMessage($message, $to_number);
    public function sendMediaMessage($text, $to, $reply_to, $link, $original_name, $referenceId = "");
    public function sendReplyButton(string $text, array $buttons, $to);
    public function sendListMessage($list, $to);
    public function sendLocation($location, $to, $referenceId = "");
    // Session Controlling Operations
    //
    public function redeploy();

    //Session Information Getters
    //
    public function getQr();
    public function getScreen();
    public function getStatus();


    public function getProvider();
}
