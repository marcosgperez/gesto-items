<?php
namespace App\Http\Controllers;
use App\Models\FifoMessages;
use Illuminate\Http\Request;
use App\Models\Conversations;
use Carbon\Carbon;
use App\Services\EvolutionService;
class WhatsappController extends Controller
{
    public function getBotMode(Request $request)
    {
        $validated = $this->customValidate($request, [
            'phone' => 'required|string',
            'client_id' => 'required|int',
            'bot_mode' => 'required|int'
        ]);
        $data = [
            'bot_mode' => $validated['bot_mode']
        ];
        $conversation = Conversations::where('client_id', $validated['client_id'])
            ->where('phone', $validated['phone'])
            ->orderBy('chat_open_timestamp', 'desc')
            ->first();
        if (empty($conversation)) {
            $conversation = new Conversations();
            $conversation->phone = $validated['phone'];
            $conversation->client_id = $validated['client_id'];
            $conversation->bot_mode = $validated['bot_mode'];
            $conversation->chat_open_timestamp = date('Y-m-d H:i:s');
            try {
                $conversation->save();
                return $this->resultOk($data);
            } catch (\Exception $error) {
                return $this->resultError($data);
            }
        } else {
            if (!empty($conversation->chat_open_timestamp)) {
                $time_window_constant = 2;
                $now = Carbon::now();
                $phone_open_time = Carbon::parse($conversation->chat_open_timestamp);
                $diff = $now->diffInHours($phone_open_time);
                if ($diff >= $time_window_constant) {
                    $conversation->chat_open_timestamp = $validated['bot_mode'] == 1 ? date('Y-m-d H:i:s') : null;
                    $conversation->chat_close_timestamp = $validated['bot_mode'] == 1 ? null : date('Y-m-d H:i:s');
                    $conversation->bot_mode = $validated['bot_mode'];
                    $data['bot_mode'] = $conversation->bot_mode;
                }  else {
                    $data['bot_mode'] = $conversation->bot_mode;
                    $conversation->chat_open_timestamp = date('Y-m-d H:i:s');
                }
            } else {
                $conversation->chat_open_timestamp = $validated['bot_mode'] == 1 ? date('Y-m-d H:i:s') : null;
                $conversation->chat_close_timestamp = $validated['bot_mode'] == 1 ? null : date('Y-m-d H:i:s');
                $conversation->bot_mode = $validated['bot_mode'];
                $data['bot_mode'] = $conversation->bot_mode;
            }
            try {
                $conversation->save();
                return $this->resultOk($data);
            } catch (\Exception $error) {
                return $this->resultError($data);
            }

        }
    }

    public function send_fifo_messages (Request $request) {
        $validated = $this->customValidate($request, [
            'instance' => 'required|string'
        ]);
        $data = [];
        $messages = FifoMessages::where('instance', $validated['instance'])
            ->where('send_timestamp', null)
            ->orderBy('id', 'asc')
            ->get();
        if (empty($messages)) {
            return $this->resultOk([]);
        } else {
            $evolution = new EvolutionService();
            foreach ($messages as $message) {
                $body = [
                    'number' => $message->phone,
                    'textMessage' => [
                        'text' => $message->message,
                    ]
                ];
                $response = $evolution->sendMessage($message->instance,  $body);
                if (!($response['status'] == 400)) {
                    $message->send_timestamp = date('Y-m-d H:i:s');
                    $message->save();
                } else {
                    $message->errors = $response['message'];
                    $message->send_timestamp = date('Y-m-d H:i:s');
                    $message->save();
                }
                $data[] = $message->id;
            }
            return $this->resultOk($data);
        }
    }
}