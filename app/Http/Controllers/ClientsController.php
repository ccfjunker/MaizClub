<?php

namespace App\Http\Controllers;

use App\Http\Models\Client;
use App\Http\Models\Parameters;
use App\Http\Models\PointsLog;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Intervention\Image\Facades\Image;

class ClientsController extends BaseController
{
	public function postFacebookLogin()
	{
		$v_Validator = Validator::make(Input::all(), Client::$rulesFacebook);
		if (!$v_Validator->passes())
			throw new AccessDeniedHttpException('auth_fail');

        $v_FacebookAccessToken = Input::get('access_token');

        $v_FBInfo = json_decode(file_get_contents("https://graph.facebook.com/me?access_token=" . $v_FacebookAccessToken), true);

        $v_Client = null;
        if(isset($v_FBInfo['email']))
            $v_Client = Client::where('email', '=', $v_FBInfo['email'])->where('facebook_id', '=', $v_FBInfo['id'])->first();
        if ($v_Client == null) //client didn't have facebook account linked, or changed email
        {
            if(isset($v_FBInfo['email']))
                $v_Client = Client::where('email', '=', $v_FBInfo['email'])->first();
            if ($v_Client != null)
                $v_Client->facebook_id = $v_FBInfo['id'];
            else {
                $v_Client = Client::where('facebook_id', '=', $v_FBInfo['id'])->first();
                if ($v_Client != null && isset($v_FBInfo['email']))
                    $v_Client->email = $v_FBInfo['email'];
                else if($v_Client != null && $v_Client->email == null)
                    $v_Client->email = $v_FBInfo['id'];
            }
        }
        if ($v_Client == null) //new client
        {
            $v_Client = new Client;
            $v_Client->email = isset($v_FBInfo['email']) ? $v_FBInfo['email'] : $v_FBInfo['id'];
            $v_Client->name = $v_FBInfo['name'];
            $v_Client->facebook_id = $v_FBInfo['id'];
        }

        $v_Url = 'http://graph.facebook.com/' . $v_FBInfo['id'] . '/picture?redirect=true&type=large&height=200&width=200';
        $v_Client->photo_url = Client::saveClientPhoto($v_Url, $v_Client->photo_url);
        $v_Client->save();


        Auth::guard('client')->login($v_Client);
        return  Client::getWithLoginDetails(Auth::guard('client')->id());
	}

	public function addClient()
	{
		$v_Validator = Validator::make(Input::all(), Client::$rules);
		if (!$v_Validator->passes()) {
            return redirect()->back()->with('error_message', 'Os seguintes erros foram encontrados')->withErrors($v_Validator)->withInput();
        }
        Client::createNew(Input::get('cpf'),
            Input::get('email'),
            Input::get('name'),
            Input::get('password')
        );

        return redirect(url('/client/search'))->with('message', 'O registro foi efetuado com sucesso.');
	}

	public function mobileRegister()
	{
		$v_Validator = Validator::make(Input::all(), Client::$mobileRules);
		if (!$v_Validator->passes()) {
            throw new BadRequestHttpException($v_Validator->errors()->all()[0]);
        }
        if(Input::hasFile('image_file') && Input::file('image_file')->isValid())
            $v_PhotoUrl = Client::saveClientPhoto(Input::file('image_file'));
        else
            $v_PhotoUrl = null;
        $v_Client = Client::createNew(Input::get('cpf'),
            Input::get('email'),
            Input::get('name'),
            Input::get('password'),
            $v_PhotoUrl
        );

        Auth::guard('client')->login($v_Client);
        return  Client::getWithLoginDetails(Auth::guard('client')->id());
	}

	public function updateClient()
    {
        $v_Validator = Validator::make(Input::all(), Client::getEditRules(Input::get('client_id')));
        if (!$v_Validator->passes()) {
            if ($this->isAdmin())
                return redirect(url('/client/edit/' . Input::get('client_id')))
                    ->with('error_message', 'Os seguintes erros foram encontrados')
                    ->withErrors($v_Validator)
                    ->withInput();
            else
                throw new BadRequestHttpException($v_Validator->errors()->all()[0]);
        }
        if (($this->isAdmin() &&
                Auth::guard('company')->attempt(array('id' => Auth::guard('company')->id(), 'password' => Input::get('password')))) ||
            Auth::guard('client')->id() == Input::get('client_id')
        ) {
            Client::updateClient(
                    Input::get('client_id'),
                    Input::get('cpf'),
                    Input::get('email'),
                    Input::get('name'),
                    Input::get('new_password')
            );

            if (Input::hasFile('image_file') && Input::file('image_file')->isValid()) {
                $v_Client = Client::find(Input::get('client_id'));
                $v_Client->photo_url = Client::saveClientPhoto(Input::file('image_file'), $v_Client->photo_url);
                $v_Client->save();
            }

            if ($this->isAdmin())
                return redirect(url('/client/search/'))->with('message', 'Edição Realizada!');
            else
                return Client::getWithLoginDetails(Auth::guard('client')->id());
        }
        else {
            if ($this->isAdmin())
                return redirect(url('/client/edit/' . Input::get('client_id')))->with('error_message', 'Senha não confere!');
            else
                throw new AccessDeniedHttpException('not_authorized');
        }
    }

    public function deactivateClient($p_ClientId) {
        return Client::deactivate($p_ClientId);
    }

    public function getEditClientLayout($p_ClientId = null)
    {
		if ($p_ClientId != null)
			$v_Client = Client::find($p_ClientId);
		else
			$v_Client = null;
        return view('clients.edit', array('p_Client' => $v_Client));
    }

	public function searchClient()
	{
		return view('clients.search');
	}

	public function getStatistics($p_ClientId)
	{
		$v_Client = Client::find($p_ClientId);
		$v_PointsLogQuery = PointsLog::with('offer')->where('client_id', '=', $p_ClientId);
		if($this->isAdmin())
			$v_PointsLog = $v_PointsLogQuery->with('company')->get();
		else
		{
			$v_PointsLog = $v_PointsLogQuery->where('company_id', '=', Auth::guard('company')->id())->get();
			if (sizeof($v_PointsLog->toArray()) == 0)
				return redirect(url('client/search'))->with('error_message', 'O cliente não foi encontrado ou não tem atividade em seu estabelecimento');
		}
		return view('clients.statistics', array('p_PointsLog' => $v_PointsLog, 'p_Client' => $v_Client));
	}

    public function getDTClients()
    {
        $v_Columns = Input::get('columns');
        $v_Name = $v_Columns[1]['search']['value'];
        $v_CPF = $v_Columns[2]['search']['value'];
        $v_Email = $v_Columns[3]['search']['value'];
        $v_Status = $this->isAdmin() ? $v_Columns[4]['search']['value'] : '';
        $v_Order = Input::get('order')[0];
        $v_Start = Input::get('start');
        $v_Length = Input::get('length');
        $v_Draw = Input::get('draw');
        return Client::getDTClients($v_Name, $v_CPF, $v_Email, $v_Status, $v_Order, $v_Start, $v_Length, $v_Draw);
    }
}
?>