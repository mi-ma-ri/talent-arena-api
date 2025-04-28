<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class RegisterController extends Controller
{
    public function getEmailAuthKey(Request $request)
    {
        dd(11);
        $result = [
            'result_code' => 200,
            'result_message' => 'OK',
            'auth_key' => 'ok'
        ];

        return response()->json($result);
    }
}
