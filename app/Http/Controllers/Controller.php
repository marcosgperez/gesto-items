<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function customValidate($request, $rules, $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            throw new ValidationException($validator, response(['ok' => 0, 'message' => $validator->errors()->first(), "errors" => $validator->errors()->all()], 400));
        }
        return $validator->validated();
    }
    protected function resultOk($data, string $message = null)
    {
        $resp = [
            'ok' => 1,
            'data' => []
        ];
        if ($message) {
            $resp['message'] = $message;
        }
        if ($data) {
            if (gettype($data) === 'object' && get_class($data) === "Illuminate\Pagination\LengthAwarePaginator") {
                $resp = $resp + $data->toArray();
            } else {
                $resp['data'] = $data;
            }
        }
        return response($resp, 200);
    }
    protected function resultError($message = "Ha sucedido un error", $data = null, $errors = null, $status = 404)
    {
        $resp = [
            'ok' => 0,
            'message' => $message
        ];
        if ($data != null) {
            $resp['data'] = $data;
        }
        if ($errors != null) {
            $resp['errors'] = $errors;
        }
        return response($resp, $status);
    }
}
