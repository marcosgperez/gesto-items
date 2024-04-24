<?php

namespace App\Http\Controllers;

use App\Adapters\WhatsappProviderAdapter;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\Events;
use App\Models\Checks;
use App\Models\FifoMessages;
use App\Models\WhatsappPhones;
use Carbon\Carbon;

class ChecksController extends Controller
{
    private function _generateAndUploadQr($itemId)
    {
        $frontUrl = "$itemId";
        $fileName = "qr-code-$itemId.png";
        $qrCode = QrCode::size(200)->format('png')->generate($frontUrl);
        Storage::disk('s3')->put($fileName, $qrCode);
        $url = 'https://gesto-items.s3.amazonaws.com/' . $fileName;
        return $url;
    }

    public function createCheck(Request $request)
    {
        $customValidated = $this->customValidate($request, [
            'amount' => 'required',
            'payment_date' => 'required|date_format:"d-m-Y"',
            'from' => 'required|string',
            'to' => 'required|string',
            'instance' => 'required|string',
            'phone' => 'required|string'
        ]);
        try {
            $payment_date = Carbon::createFromFormat('d-m-Y', $customValidated['payment_date']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Formato de fecha inválido. Use "dd-mm-yyyy" o "dd/mm/yyyy".'], 422);
        }
        $check = new Checks();
        $check->amount = $customValidated['amount'];
        $check->payment_date = $payment_date;
        $check->from = $customValidated['from'];
        $check->to = $customValidated['to'];
        $check->instance = $customValidated['instance'];
        $check->phone = $customValidated['phone'];
        $check->status = '1';
        $check->save();

        return response()->json($check);
    }


    public function remind_check()
    {
        $data = [];
        $checks = Checks::where('status', '1')
        ->where('payment_date', '<=', Carbon::now()->addDays(3)->format('Y-m-d'))
        ->get();
        foreach ($checks as $check) {
            $check_type = $check->from == 'entrante' ? 'Cheque a cobrar' : 'Cheque a pagar';
            $text_to_send = $check_type . "\n" .
                "Cantidad: " . $check->amount . "\n" .
                "Día: " . $check->payment_date;
            $whatsappServiceProvider = WhatsappProviderAdapter::new($check->instance);
            $whatsappServiceProvider->sendTextMessage(
                $text_to_send,
                $check->phone
            );
            $check->status = '2';
            $check->save();
            $data[] = $check;
        }
        return $this->resultOk($data);
    }

}