<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RestorePassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RestoreController extends Controller
{
    public function restore(Request $request, User $user)
    {
        $data = $request->getContent(); // получаем body запроса
        $credential = json_decode($data, true); // переводим в ассоциативный массив
        $validator = Validator::make($credential, [
            'email' => 'required|email|exists:users,email'
        ], [], []);

        // Check validation failure
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ])
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        if ($validator->passes()) {
            $email = $credential['email'];
            $user = User::where('email', $email)->first();
            $password = rand(0, 99999) . 'qwdasdw';
            $user->fill([
                'password' => Hash::make($password)
            ]);
            $user->save();
            mail($email, 'Восстановление пароля', 'ваш пароль ' . $password, 'From: sender@example.com');
            return response()->json([
                'status' => 'success',
                'message' => 'Пароль отправлен на почту ' . $email
            ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
}
