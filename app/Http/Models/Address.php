<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
	protected $table = 'address';

	public $timestamps = false;

	public function company()
	{
		return $this->belongsTo('App\Http\Models\Company');
	}

	public function companyAuths()
	{
		return $this->hasMany('App\Http\Models\CompanyAuth');
	}

    public static $rules = array(
        'company_id'=>'required|numeric',
        'name'=>'min:1',
        'tel'=>'numeric|digits_between:10,11',
        'select_city'=>'required|min:1',
        'select_state'=>'required|min:1',
        'country'=>'required|min:1',
        'cep'=>'required|numeric|digits:8',
        'street'=>'required|min:1',
        'street_number'=>'required|min:1',
        'complement'=>'min:1',
        'neighborhood'=>'required|min:1',
        'latitude'=>'required|numeric',
        'longitude'=>'required|numeric'
    );

    public static function createAddress($p_CompanyId, $p_Name, $p_Tel, $p_City, $p_State, $p_Country, $p_CEP, $p_Street, $p_StreetNumber, $p_Complement, $p_Neighborhood, $p_Latitude, $p_Longitude)
    {
        $v_Address = new Address();
        $v_Address->company_id = $p_CompanyId;
        $v_Address->name = ($p_Name != null ? $p_Name : '');
        if ($p_Tel != null)
            $v_Address->tel = $p_Tel;
        $v_Address->city = $p_City;
        $v_Address->state = $p_State;
        $v_Address->country = $p_Country;
        $v_Address->cep = $p_CEP;
        $v_Address->street = $p_Street;
        $v_Address->street_number = $p_StreetNumber;
        if ($p_Complement != null)
            $v_Address->complement = $p_Complement;
        $v_Address->neighborhood = $p_Neighborhood;
        $v_Address->latitude = $p_Latitude;
        $v_Address->longitude = $p_Longitude;
        $v_Address->is_active = 1;

        $v_Address->save();
    }

    public static function updateAddress($p_AddressId, $p_Name, $p_Tel, $p_City, $p_State, $p_Country, $p_CEP, $p_Street, $p_StreetNumber, $p_Complement, $p_Neighborhood, $p_Latitude, $p_Longitude)
    {
        $v_Address = Address::find($p_AddressId);
        if ($v_Address == null)
            return false;
        $v_Address->name = ($p_Name != null ? $p_Name : '');
        if ($p_Tel != null)
            $v_Address->tel = $p_Tel;
        $v_Address->city = $p_City;
        $v_Address->state = $p_State;
        $v_Address->country = $p_Country;
        $v_Address->cep = $p_CEP;
        $v_Address->street = $p_Street;
        $v_Address->street_number = $p_StreetNumber;
        if ($p_Complement != null)
            $v_Address->complement = $p_Complement;
        $v_Address->neighborhood = $p_Neighborhood;
        $v_Address->latitude = $p_Latitude;
        $v_Address->longitude = $p_Longitude;

        $v_Address->save();
        return true;
    }
}
