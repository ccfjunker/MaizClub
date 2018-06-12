<?php

namespace App\Http\Models;

use App\Http\Controllers\BaseController;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use \Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class Client extends Authenticatable
{
    protected $guard = "client";

	protected $table = 'client';

	protected $hidden = array('password', 'remember_token', 'created_at', 'updated_at');

	public function points()
	{
		return $this->hasMany('App\Http\Models\Points');
	}

	public function pointsLog()
	{
		return $this->hasMany('App\Http\Models\PointsLog');
	}
	
	public static $rulesFacebook = array(
		'access_token'=>'required|min:1'
	);
	
	public static $mobileRules = array(
		'cpf'=>'required|numeric|digits:11|unique:client',
		'email'=>'required|email|unique:client',
		'name'=>'required|min:2',
		'password'=>'required|min:6',
		'image_file'=>'image'
	);

	public static $rules = array(
		'cpf'=>'required|numeric|digits:11|unique:client',
		'email'=>'required|email|unique:client',
		'name'=>'required|min:2',
		'password'=>'required|min:6|confirmed'
	);

	private static $editRules = array(
		'cpf'=>'required|numeric|digits:11|unique:client,cpf,{id}',
		'email'=>'required|email|unique:client,email,{id}',
		'name'=>'required|min:2',
		'password'=>'min:6',
		'new_password'=>'min:6|confirmed',
		'image_file'=>'image'
	);

	public static function getEditRules($p_Id)
	{
		$v_Rules = array();
		foreach(Client::$editRules as $c_RuleId => $c_Rule)
			$v_Rules[$c_RuleId] = str_replace('{id}', $p_Id, $c_Rule);
		return $v_Rules;
	}

    public static function deactivate($p_ClientId)
    {
        $v_Client = Client::find($p_ClientId);
        if($v_Client == null)
            return redirect()->back()->with('error_message', 'Cliente não encontrado.');

        if ($v_Client->deactivated == null)
            $v_Client->deactivated = Carbon::now()->format('Y-m-d H:i:s');
        else
            $v_Client->deactivated = null;
        $v_Client->save();

        $v_Error['error'] = 'ok';
        return json_encode($v_Error);
    }

    public function isActive()
    {
        return $this->deactivated == null;
    }

	public static function setActiveCheckin($p_Id, $p_CompanyId)
	{
		$v_Client = Client::find($p_Id);
		$v_Client->active_checkin = $p_CompanyId;
		$v_Client->save();
	}

    public static function getDTClients($p_Name, $p_CPF, $p_Email, $p_Status, $p_Order, $p_Start, $p_Length, $p_Draw)
    {
        if (BaseController::isAdmin())
            $v_Query = Client::whereNotNull("id");
        else
        {
            $v_CompanyId = Auth::guard('company')->id();
            $v_Query = Client::whereNotNull("id")->with('pointsLog')->whereHas('pointsLog', function($p_Query) use($v_CompanyId)
            {
                $p_Query->where('company_id','=',$v_CompanyId);
            });
        }
        if($p_Name != '')
        {
            $v_Query->where('name', 'LIKE', '%' . $p_Name . '%');
        }

        if($p_CPF != '')
        {
            $v_UnwantedCharacters = array('.', '-');
            $v_Query->where('cpf', 'LIKE', '%' . str_replace($v_UnwantedCharacters, '', $p_CPF) . '%');
        }

        if($p_Email != '')
        {
            $v_Query->where('email', 'LIKE', '%' . $p_Email . '%');
        }

        if($p_Status != '')
        {
            if ($p_Status == 0)
                $v_Query->whereNotNull('deactivated');
            else
                $v_Query->whereNull('deactivated');
        }

        if($p_Order != null)
        {
            if($p_Order["column"] == 0)
                $v_Query->orderBy('id', $p_Order["dir"]);
            else if($p_Order["column"] == 1)
                $v_Query->orderBy('name', $p_Order["dir"]);
            else if($p_Order["column"] == 2)
                $v_Query->orderBy('cpf', $p_Order["dir"]);
            else if($p_Order["column"] == 3)
                $v_Query->orderBy('email', $p_Order["dir"]);
        }

        $v_QueryRes = $v_Query->get();
        $v_Data = [];
        $v_DataTableAjax = new \stdClass();
        $v_DataTableAjax->recordsTotal = sizeof($v_QueryRes);
        $v_DataTableAjax->recordsFiltered = sizeof($v_QueryRes);
        if($p_Length != -1)
            $v_QueryRes = $v_QueryRes->slice($p_Start, $p_Length);

        foreach($v_QueryRes as $c_Client)
        {
            $v_CPF = !$c_Client->cpf ? '' : substr($c_Client->cpf, 0, 3) . '.' . substr($c_Client->cpf, 3, 3) . '.' . substr($c_Client->cpf, 6, 3) . '-' . substr($c_Client->cpf, 9, 2);
            if (BaseController::isAdmin())
            {
                array_push($v_Data,
                    [
                        $c_Client->id,
                        $c_Client->name,
                        $v_CPF,
                        $c_Client->email,
                        '<i class="fa '.($c_Client->isActive() ? 'fa-check' : 'fa-times').'"></i>',
                        '<a href="' . url('/client/statistics/' . $c_Client->id) . '" title="Estatísticas" type="button" class="btn btn-primary"><i class="ico-stats"></i></a>'.
                        '<a href="' . url('/client/edit/' . $c_Client->id) . '" title="Editar" type="button" class="btn btn-primary"><i class="ico-pencil"></i></a>'
                    ]);
            }
            else
            {
                array_push($v_Data,
                    [
                        $c_Client->id,
                        $c_Client->name,
                        $v_CPF,
                        $c_Client->email,
                        '<a href="' . url('/client/statistics/' . $c_Client->id) . '" title="Estatísticas" type="button" class="btn btn-primary"><i class="ico-stats"></i></a>'
                    ]);
            }
        }

        $v_DataTableAjax->draw = $p_Draw;
        $v_DataTableAjax->data = $v_Data;
        return json_encode($v_DataTableAjax);
    }

    public static function createNew($p_Cpf, $p_Email, $p_Name, $p_Pwd, $p_PhotoUrl = null) {
        $v_Client = new Client;
        $v_Client->cpf = $p_Cpf;
        $v_Client->email = $p_Email;
        $v_Client->name = $p_Name;
        $v_Client->password = Hash::make($p_Pwd);
        if($p_PhotoUrl == null)
            $v_Client->photo_url = url('/images/clients/avatar.jpg');
        else
            $v_Client->photo_url = $p_PhotoUrl;
        $v_Client->save();
        return $v_Client;
    }

    public  static function updateClient($p_Id, $p_Cpf, $p_Email, $p_Name, $p_Pwd) {
        $v_Client = Client::find($p_Id);
        $v_Client->cpf = $p_Cpf;
        $v_Client->email = $p_Email;
        $v_Client->name = $p_Name;
        if ($p_Pwd != NULL)
            $v_Client->password = Hash::make($p_Pwd);
        $v_Client->save();
    }

    public static function getWithLoginDetails($p_Id) {
        $v_Client = Client::leftJoin('points', 'points.client_id', '=', 'client.id')
            ->where(function ($query){
                $query->where('points.company_id', '1')
                      ->orWhereNull('points.company_id');
            })
            ->select(DB::raw('client.*, coalesce(points.value, 0) as bonusPoints'))
            ->groupBy('client.id')
            ->find($p_Id);
        $v_Client->attributes['hasPwd'] = !($v_Client['password'] === null || $v_Client['password'] === "");
        $v_Client->attributes['bonus_point_rate'] = Parameters::getBonusPointsRate();
//        $v_Client->attributes['totalPoints'] = Points::where('company_id', '!=', 1)
//            ->where('client_id', '=', Auth::guard('client')->id())
//            ->sum('value');
        return $v_Client;
    }

    public static function saveClientPhoto($p_ImgFile, $p_OldFile = null) {
        $v_Path = public_path() . '/images/clients/';
        if (!\File::exists($v_Path))
            \File::makeDirectory($v_Path, 493, true);

        if ($p_OldFile != null)
        {
            $v_OldFileName = explode('/', $p_OldFile);
            $v_OldFileName = array_pop($v_OldFileName);
            \File::delete($v_Path . $v_OldFileName);
        }

        $v_Img = Image::make($p_ImgFile);

        $v_Img->widen(200, function ($constraint){
            $constraint->upsize();
        });
        $v_Width = $v_Img->width();
        $v_Height = $v_Img->height();
        if($v_Width != $v_Height)
        {
            $v_Size = $v_Width < $v_Height ? $v_Width : $v_Height;
            $v_Img->crop($v_Size, $v_Size, floor(($v_Width - $v_Size)/2), floor(($v_Height - $v_Size)/2));
        }
        $v_ImgName =  time() . str_random(10) . '.jpg';
        $v_Img->encode('jpg')->save($v_Path . $v_ImgName);
        return url('/images/clients/' . $v_ImgName);
    }
}
