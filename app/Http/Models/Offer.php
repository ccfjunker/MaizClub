<?php

namespace App\Http\Models;

use App\Http\Controllers\BaseController;
use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class Offer extends Model
{
	protected $table = 'offer';

	public $timestamps = false;
	
	public function company()
	{
		return $this->belongsTo('App\Http\Models\Company');
	}

	public function rules()
	{
		return $this->hasMany('App\Http\Models\OfferRules');
	}

	public function pointsLog()
	{
		return $this->hasMany('App\Http\Models\PointsLog');
	}

	public static $addRules = array(
		'company_id'=>'required|numeric',
		'title'=>'required|min:1',
		'points'=>'required|numeric',
		'price'=>'required|regex:/\d+[\.?\d{1,2}]{0,1}/',
		'old_price'=>'regex:/\d+[\.?\d{1,2}]{0,1}/',
		'description'=>'required|min:1',
		'image_file'=>'required|image',
		'activation_date'=>'required|date_format:d/m/Y',
		'valid_until'=>'required|date_format:d/m/Y|after:activation_date',
		'amount_allowed'=>'required|numeric',
		'is_prize'=>'required|boolean',
		'rule'=>'required|min:1'
	);

	public static $editRules = array(
		'company_id'=>'required|numeric',
		'title'=>'required|min:1',
		'points'=>'required|numeric',
		'price'=>'required|regex:/\d+[\.?\d{1,2}]{0,1}/',
		'old_price'=>'regex:/\d+[\.?\d{1,2}]{0,1}/',
		'description'=>'required|min:1',
		'image_file'=>'image',
		'activation_date'=>'required|date_format:d/m/Y',
		'valid_until'=>'required|date_format:d/m/Y|after:activation_date',
		'amount_allowed'=>'required|numeric',
		'rule'=>'required|min:1'
	);

	public static $rulesGetOffers = array(
		'company_id'=>'required|numeric',
		'is_prize'=>'required|boolean'
	);

	public function isActive()
	{
		if($this->date_deactivated != null)
			return false;
		$v_ActivationDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->activation_date);
		if(Carbon::now()->lt($v_ActivationDate))
			return false;
		$v_ValidUntil = Carbon::createFromFormat('Y-m-d H:i:s', $this->valid_until);
		if(Carbon::now()->gt($v_ValidUntil))
			return false;
		if($this->amount_used >= $this->amount_allowed)
			return false;
		return true;
	}

	public function isEditable()
	{
        if($this->date_deactivated != null)
            return false;
        $v_ActivationDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->activation_date);
        if(Carbon::now()->lt($v_ActivationDate))
            return true;
        $v_ValidUntil = Carbon::createFromFormat('Y-m-d H:i:s', $this->valid_until);
        if(Carbon::now()->gt($v_ValidUntil))
    		return false;
        if(BaseController::isAdmin())
            return true;
	}

	public function isRuleEditable()
	{
        if($this->date_deactivated != null)
            return false;
        $v_ActivationDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->activation_date);
        if(Carbon::now()->lt($v_ActivationDate))
            return true;
        $v_ValidUntil = Carbon::createFromFormat('Y-m-d H:i:s', $this->valid_until);
        if(Carbon::now()->lt($v_ValidUntil))
    		return true;
	}

	private static function getActiveOfferQuery()
	{
		$v_Now = Carbon::now()->format('Y-m-d H:i:s');
		return Offer::where('activation_date', '<=', $v_Now)
		                 ->where('valid_until', '>', $v_Now)
		                 ->whereNull('date_deactivated');
	}

	public static function getActive($p_Id)
	{
		return Offer::getActiveOfferQuery()->find($p_Id);
	}

	public static function getActiveFromCompany($p_Id, $p_CompanyId)
	{
		return Offer::getActiveOfferQuery()->where('company_id', '=', $p_CompanyId)->find($p_Id);
	}

	public static function getFromCompany($p_Id)
	{
		return Offer::where('company_id', '=', Auth::guard('company')->id())->find($p_Id);
	}

	public static function claimOffer($p_OfferId, $p_Password)
    {
        $v_Offer = Offer::find($p_OfferId);
        $v_Client = Auth::guard('client')->user();
        if ($v_Offer == null)
            throw new NotAcceptableHttpException('offer_doesnt_exist');
        if ($v_Offer->date_deactivated != null)
            throw new NotAcceptableHttpException('offer_deactivated');
        $v_ActivationDate = Carbon::createFromFormat('Y-m-d H:i:s', $v_Offer->activation_date);
        if (Carbon::now()->lt($v_ActivationDate))
            throw new NotAcceptableHttpException('offer_not_activated');
        $v_ValidUntil = Carbon::createFromFormat('Y-m-d H:i:s', $v_Offer->valid_until);
        if (Carbon::now()->gt($v_ValidUntil))
            throw new NotAcceptableHttpException('offer_expired');
        if (intval($v_Offer->amount_used) >= intval($v_Offer->amount_allowed))
            throw new NotAcceptableHttpException('offer_sold_out');
        if ($v_Offer->is_prize == 1 && !Points::hasEnough($v_Client->id, $v_Offer->company_id, $v_Offer->points))
            throw new NotAcceptableHttpException('insufficient_points');
        if ($v_Offer->company_id != 1) {
            if ($v_Offer->is_prize == 0 && PointsLog::hasClaimedOffer($v_Client->id, $v_Offer->id))
                throw new NotAcceptableHttpException('offer_already_bought');
        }
        $v_AuthId = CompanyAuth::validate($v_Offer->company_id, $p_Password);
        if ($v_AuthId == null)
            throw new NotAcceptableHttpException('offer_company_auth_fail');
        DB::beginTransaction();
        try {
            if ($v_Offer->is_prize == 0)
                PointsLog::offerLog($v_Client->id, $v_Offer->company_id, $v_AuthId, $v_Offer->id, $v_Offer->price, $v_Offer->points, $v_Offer->company_id != 1);
            else {
                PointsLog::prizeLog($v_Client->id, $v_Offer->company_id, $v_AuthId, $v_Offer->id, $v_Offer->price, $v_Offer->points);
                Points::removePoints($v_Client->id, $v_Offer->company_id, $v_Offer->points);
            }
            $v_Offer->amount_used++;
            $v_Offer->save();
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new NotAcceptableHttpException('error_saving');
        }
    }

	public static $m_OfferSelect = ['offer.id', 'offer.company_id', 'offer.title', 'offer.price', 'offer.old_price', 'offer.points', 'offer.enabled_at', 'offer.description', 'offer.is_prize', 'last_rule.rule', 'offer.photo_url'];
	public static $m_LastRuleJoin = '(select t1.* from offer_rules t1 join (select max(id) as id, offer_id from offer_rules group by(offer_id)) as t2 on t1.id = t2.id) as last_rule';

	public static function getBonusPrizes()
	{
		return Offer::select(Offer::$m_OfferSelect)
			->join(DB::raw(Offer::$m_LastRuleJoin), 'offer.id', '=', 'last_rule.offer_id')
			->where('activation_date', '<=', Carbon::now()->endOfDay()->format('Y-m-d H:i:s'))
			->where('valid_until', '>=', Carbon::now()->startOfDay()->format('Y-m-d H:i:s'))
			->whereRaw('amount_allowed > amount_used')
			->whereNull('date_deactivated')
			->where('company_id', '=', 1)
			->get();
	}

	public static function createOffer($p_CompanyId, $p_Title, $p_Points, $p_Price, $p_OldPrice, $p_Description, $p_EnabledAt,
		$p_ActivationDate, $p_ValidUntil, $p_AmountAllowed, $p_IsPrize)
	{
		$v_Offer = new Offer();
		$v_Offer->company_id = $p_CompanyId;
		$v_Offer->title = $p_Title;
		$v_Offer->points = $p_Points;
		$v_Offer->price = $p_Price;
		$v_Offer->old_price = $p_OldPrice;
		$v_Offer->description = $p_Description;
		$v_Offer->enabled_at = $p_EnabledAt;
		$v_Offer->activation_date = $p_ActivationDate;
		$v_Offer->valid_until = $p_ValidUntil;
		$v_Offer->amount_allowed = $p_AmountAllowed;
		$v_Offer->amount_used = 0;
		$v_Offer->is_prize = $p_IsPrize;
		$v_Offer->save();
		return $v_Offer->id;
	}

	public static function editOffer($p_Id, $p_CompanyId, $p_Title, $p_Points, $p_Price, $p_OldPrice, $p_Description, $p_EnabledAt,
		$p_ActivationDate, $p_ValidUntil, $p_AmountAllowed, $p_AmountUsed, $p_IsPrize)
	{
		$v_Offer = Offer::find($p_Id);
		$v_Offer->company_id = $p_CompanyId;
		$v_Offer->title = $p_Title;
		$v_Offer->points = $p_Points;
		$v_Offer->price = $p_Price;
		$v_Offer->old_price = $p_OldPrice;
		$v_Offer->description = $p_Description;
		$v_Offer->enabled_at = $p_EnabledAt;
		$v_Offer->activation_date = $p_ActivationDate;
		$v_Offer->valid_until = $p_ValidUntil;
		$v_Offer->amount_allowed = $p_AmountAllowed;
		$v_Offer->amount_used = $p_AmountUsed;
		$v_Offer->is_prize = $p_IsPrize;
		$v_Offer->save();
		return $v_Offer->id;
	}

	public static function createOfferRule($p_OfferId, $p_Rule)
	{
		$Rule = new OfferRules();
		$Rule->offer_id = $p_OfferId;
		$Rule->rule = $p_Rule;
		$Rule->save();
	}

	public static function deactivate($p_Id)
	{
		$v_Offer = Offer::getActive($p_Id);
		$v_Offer->date_deactivated = Carbon::now();
		$v_Offer->save();
	}

	public static function getOffers($p_CompanyTypeId, $p_Offset, $p_Amount, $p_ClientId)
	{
		$v_Query = Offer::with('company')
						->join('company', 'offer.company_id', '=', 'company.id')
		                ->join(DB::raw(Offer::$m_LastRuleJoin), 'offer.id', '=', 'last_rule.offer_id')
		                ->where('offer.activation_date', '<=', Carbon::now()->endOfDay()->format('Y-m-d H:i:s'))
		                ->where('offer.valid_until', '>=', Carbon::now()->startOfDay()->format('Y-m-d H:i:s'))
		                ->whereRaw('offer.amount_allowed > offer.amount_used')
		                ->whereNull('offer.date_deactivated')
		                ->where('offer.company_id', '!=', 1)
		                ->select(Offer::$m_OfferSelect);

		if($p_CompanyTypeId != null)
			$v_Query->where('company.company_type_id', '=', $p_CompanyTypeId);
		if($p_Offset != null)
			$v_Query->skip($p_Offset);
		if($p_Amount != null)
			$v_Query->take($p_Amount);
		return $v_Query->get();
	}
}
