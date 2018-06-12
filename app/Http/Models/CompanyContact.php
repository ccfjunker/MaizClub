<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyContact extends Model
{
	protected $table = 'company_contact';

	public $timestamps = false;

	public static $rules = array(
		'company_id'=>'required|numeric',
		'id'=>'numeric',
		'name'=>'required|min:2',
		'email'=>'required|email|unique:company_contact,email',
		'cpf'=>'required|numeric|digits:11'
	);

	public static $editRules = array(
		'company_id'=>'required|numeric',
		'id'=>'numeric',
		'name'=>'required|min:2',
		'email'=>'required|email|unique:company_contact,email,{id}',
		'cpf'=>'required|numeric|digits:11'
	);

	public function company()
	{
		return $this->belongsTo('App\Http\Models\Company');
	}

    public static function getEditRules($p_Id)
    {
        $v_Rules = array();
        foreach(CompanyContact::$editRules as $c_RuleId => $c_Rule)
            $v_Rules[$c_RuleId] = str_replace('{id}', $p_Id, $c_Rule);
        return $v_Rules;
    }
}
