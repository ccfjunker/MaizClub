<?php

namespace App\Http\Controllers;
use App\Http\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BaseController extends Controller {
    public function getRoot() {
        if ($this->isCompany())
            return redirect()->action('OfferController@getOfferListLayout');
        else if ($this->isAdmin())
            return redirect()->action('ClientsController@searchClient');
        else
            return view('clients.login');
    }

    public function postLogin() {
        if (!Auth::guard('client')->attempt(array('email' => Input::get('user'), 'password' => Input::get('password')))) {
            if (!Auth::guard('client')->attempt(array('cpf' => Input::get('user'), 'password' => Input::get('password')))) {
                if (!Auth::guard('company')->attempt(array('email' => Input::get('user'), 'password' => Input::get('password')))) {
                    if (!Auth::guard('company')->attempt(array('cnpj' => Input::get('user'), 'password' => Input::get('password'))))
                        return redirect(url('/'))->with('error_message', 'A combinação de usuário e senha está incorreta');
                }
            }
        }
        return redirect(url('/'));
    }

    public function postMobileLogin() {
        $v_User = Input::get('user');
        $v_Pwd = Input::get('password');
        if (!Auth::guard('client')->attempt(['email' => $v_User, 'password' => $v_Pwd]) && !Auth::guard('client')->attempt(['cpf' => $v_User, 'password' => $v_Pwd]))
                throw new AccessDeniedHttpException('auth_fail');
        return Client::getWithLoginDetails(Auth::guard('client')->id());
    }

    public function getLogout() {
        if ($this->isClient())
            Auth::guard('client')->logout();
        else
            Auth::guard('company')->logout();
        return redirect(url('/'));
    }

    public function getMobileLogout() {
        Auth::guard('client')->logout();
    }
}