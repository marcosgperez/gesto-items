<?php

namespace App\Adapters\Whatsapp;

interface WebhookIncomingData
{
    public function phoneId(); //Id de identificacion de la instancia del telefono del servicio de whatsapp
    public function messageText(); //Texto del mensaje
    public function messageTextOrCaption(); //Texto del mensaje o caption de la imagen
    public function setMessageText(string $text); //Setea el texto del mensaje
    public function originalObject(); //Objeto original del webhook recibido

    public function userImage(); //Imagen de perfil del usuario
    public function conversation(); // Identificador de la conversacion [numero]@c.us o [numero]@g.us
    public function userName(); //Nombre del usuario que envio el mensaje
    public function userId(); // [numero]@c.us del usuario que envio el mensaje 
    public function userPhone(); //Numero de telefono del usuario que envio el mensaje

    public function messageType(); //Tipo de mensaje (text, image, video, etc)
    public function timestamp(); //timestamp en segundos del mensaje
    public function messageId(); //Id del mensaje proveido por el servicio
    public function messageSubtype(); //Subtipo de mensaje (en caso de existir)
    public function messageParticipant(); //Nombre del usuario que envio el mensaje (en caso de ser un grupo)
    public function receiver(); //[telefono]@c.us del receptor del mensaje (Es el telefono)
    public function isFromMe(); //Si el mensaje fue enviado desde el telefono o no
    // public function skipBotCheckAndSendMessage(); //Si se debe enviar el mensaje o no en el caso de que sea un mensaje de un bot
    public function mediaUrl(); //Url de la imagen/video/audio
    public function mediaMimeType(); //Tipo de archivo (image/jpeg, video/mp4, etc)
    public function conversation_name(); //Nombre de la conversacion (en caso de ser un grupo), o nombre del cliente
    public function participants(); //Participantes de la conversacion (en caso de ser un grupo)

    public function quotedMessageId(); //Id del mensaje citado
    public function quotedMessageText(); //Texto del mensaje citado
    public function quotedMessageMediaUrl(); //Url del archivo del mensaje citado
    public function quotedMessageType(); //Tipo de mensaje citado
    public function quotedMessagevCardList(); //Lista de contactos en caso de que sea un mensaje vCard
    public function isForwarded(); //Si el mensaje fue reenviado o no

    public function location(); //Ubicacion en caso de que sea un mensaje Location
    public function vCardList(); //Lista de contactos en caso de que sea un mensaje vCard

    public function composeQuotedMessage(); //Compone el mensaje citado
    public function composeMessage(); //Compone el mensaje
    public function composeUser(); //Compone el usuario que envio el mensaje

    public function serviceProvider();

    public function interactive_reply_id(); //Id de la respuesta interactiva
    //ack
    public function ackStatus(); //ack del mensaje

    public function isTest(); //Si el mensaje es de prueba o no
}
