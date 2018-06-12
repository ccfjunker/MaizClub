<?php

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Points extends Model
{
	protected $table = 'points';

	public $timestamps = false;
	
	protected $fillable = array('client_id', 'company_id');

	public function company()
	{
		return $this->belongsTo('App\Http\Models\Company');
	}
	
	public function client()
	{
		return $this->belongsTo('App\Http\Models\Client');
	}

	protected function performUpdate(Builder $query, array $options = array())
	{
		DB::update(DB::raw("UPDATE " . $this->getTable() . " SET value = " . $this->attributes['value'] . " WHERE client_id = " . $this->attributes['client_id'] . " AND company_id = " . $this->attributes['company_id']));
	}

	public static function hasEnough($p_ClientId, $p_CompanyId, $p_Amount)
	{
		$v_Points = Points::where('client_id', '=', $p_ClientId)
			->where('company_id', '=', $p_CompanyId)
			->where('value', '>=', $p_Amount)
			->first();
		return $v_Points != null;
	}

	public static function removePoints($p_ClientId, $p_CompanyId, $p_Amount)
	{
		$v_Points = Points::where('client_id', '=', $p_ClientId)
							->where('company_id', '=', $p_CompanyId)
							->first();
		$v_Points->value -= $p_Amount;
		$v_Points->save();
	}

	public static function addPoints($p_ClientId, $p_CompanyId, $p_Amount)
	{
		$v_Points = Points::firstOrNew(array('client_id' => $p_ClientId, 'company_id' => $p_CompanyId));
		if($v_Points->value == null)
			$v_Points->value = 0;
		$v_Points->value += $p_Amount;
		$v_Points->save();
	}

	public static function addBonusPoints($p_ClientId, $p_Amount)
	{
		Points::addPoints($p_ClientId, 1, $p_Amount);
	}

	public static function getBonusPoints($p_ClientId)
	{
		$v_Points = Points::where('client_id', '=', $p_ClientId)
			->where('company_id', '=', 1)
			->first();
		return $v_Points == null ? 0 : $v_Points->value;
	}
}
