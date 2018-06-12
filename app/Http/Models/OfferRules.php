<?php

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

class OfferRules extends Model
{
	protected $table = 'offer_rules';

	public $timestamps = false;
	
	public function company()
	{
		return $this->belongsTo('App\Http\Models\Offer');
	}
}
