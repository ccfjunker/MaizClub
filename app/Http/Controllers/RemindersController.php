<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Illuminate\Support\Facades\Validator;

class RemindersController extends BaseController
{
    public function getMobileReset($p_Email)
    {
        if(Password::broker('client')->sendResetLink(['email' => $p_Email], function ($message) {
            $message->subject('QUERO - Redefinir Senha');
        }) == Password::INVALID_USER)
            throw new NotAcceptableHttpException('invalid_user');
    }

    public function sendResetLink()
    {
        $v_Email = Input::get('email');
        if(Password::broker('company')->sendResetLink(['email' => $v_Email], function ($message) {
            $message->subject('QUERO - Redefinir Senha');
        }) == Password::INVALID_USER)
        {
            if(Password::broker('client')->sendResetLink(['email' => $v_Email], function ($message) {
                    $message->subject('QUERO - Redefinir Senha');
                }) == Password::INVALID_USER)
            {
                Session::put('error_message','Usuário inválido.');
                Session::save();
                return redirect()->back();
            }
        }
        Session::put('message','Em breve você receberá um email com instruções para redefinir sua senha.');
        Session::save();
        return redirect(url('/'));
    }

    public function getReset($p_Token)
    {
        return view('clients.reset')->with('token', $p_Token);
    }

    public function getForgotPassword()
    {
        return view('clients.forgotPwd');
    }

    public function postReset()
    {
        $v_Credentials = Input::only('email', 'password', 'password_confirmation', 'token');

        $v_Validator = Validator::make($v_Credentials, [
            'email'=>'required|email',
            'token'=>'required|min:1',
            'password'=>'required|min:6|confirmed'
        ]);
        if (!$v_Validator->passes())
        {
            Session::put('error_message',$v_Validator->errors()->all()[0]);
            Session::save();
            return redirect()->back();
        }

        $v_Response = Password::broker('client')->reset($v_Credentials, function ($client, $password) {
            $client->password = Hash::make($password);
            $client->save();
        });

        switch ($v_Response) {
            case Password::INVALID_PASSWORD:
                Session::put('error_message','Senha inválida.');
                Session::save();
                return redirect()->back();
            case Password::INVALID_TOKEN:
                Session::put('error_message','Erro de token.');
                Session::save();
                return redirect()->back();
            case Password::INVALID_USER:
                Session::put('error_message','Usuário inválido.');
                Session::save();
                return redirect()->back();
            case Password::PASSWORD_RESET:
                Session::put('message','Sua senha foi alterada com sucesso.');
                Session::save();
                return redirect('/');
        }
    }
}