<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyAuth extends Model
{
	protected $table = 'company_auth';

	public $timestamps = false;

	public static $rules = array(
		'company_id'=>'required|numeric',
		'id'=>'numeric',
		'name'=>'required|min:2',
		'password'=>'required|numeric|digits:6'
	);

	public function address()
	{
		return $this->belongsTo('App\Http\Models\Address');
	}

	public static function validate($p_CompanyId, $p_Password)
	{
		$v_CompanyAuth = CompanyAuth::whereHas('address', function($p_Query) use($p_CompanyId)
		{
			$p_Query->where('company_id', '=', $p_CompanyId);
		})
			->where('is_active', '=', 1)
			->where('password', '=', $p_Password)
		    ->first();
		return $v_CompanyAuth == null ? null : $v_CompanyAuth->id;
	}

	public static function getByCompany($p_CompanyId)
	{
		return CompanyAuth::with('address')
			->whereHas('address', function($p_Query) use($p_CompanyId)
			{
				$p_Query->where('company_id', '=', $p_CompanyId);
			})
			->get();
	}


    public static function createAuth($p_CompanyId, $p_Pwd, $p_AddressId, $p_Name)
    {
        $v_AuthUsingDesiredPwd = CompanyAuth::whereHas('address', function($p_Query) use($p_CompanyId)
        {
            $p_Query->whereHas('company', function($p_Query2) use($p_CompanyId)
            {
                $p_Query2->where('id', '=', $p_CompanyId);
            });
        })->where('is_active', '=', 1)->where('password', '=', $p_Pwd)->first();
        if ($v_AuthUsingDesiredPwd != null)
            return false;

        $v_Auth = new CompanyAuth;
        $v_Auth->address_id = $p_AddressId;
        $v_Auth->name = $p_Name;
        $v_Auth->password = $p_Pwd;
        $v_Auth->is_active = 1;
        $v_Auth->save();

        return true;
    }

    public static function updateAuth($p_CompanyId, $p_Pwd, $p_AddressId, $p_Name, $p_AuthId)
    {
        $v_AuthUsingDesiredPwd = CompanyAuth::whereHas('address', function($p_Query) use($p_CompanyId)
        {
            $p_Query->whereHas('company', function($p_Query2) use($p_CompanyId)
            {
                $p_Query2->where('id', '=', $p_CompanyId);
            });
        })->where('is_active', '=', 1)->where('password', '=', $p_Pwd)->where('id', '!=', $p_AuthId)->first();
        if ($v_AuthUsingDesiredPwd != null)
            return false;

        $v_Auth = CompanyAuth::find($p_AuthId);
        $v_Auth->address_id = $p_AddressId;
        $v_Auth->name = $p_Name;
        $v_Auth->password = $p_Pwd;
        $v_Auth->is_active = 1;
        $v_Auth->save();

        return true;
    }

}
