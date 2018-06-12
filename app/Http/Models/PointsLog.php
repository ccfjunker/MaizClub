<?php

namespace App\Http\Models;

use App\Http\Controllers\BaseController;
use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PointsLog extends Model
{
	protected $table = 'points_log';

	public $timestamps = false;

	public function company()
	{
		return $this->belongsTo('App\Http\Models\Company');
	}
	
	public function client()
	{
		return $this->belongsTo('App\Http\Models\Client');
	}
	
	public function offer()
	{
		return $this->belongsTo('App\Http\Models\Offer');
	}
	
	public function companyAuth()
	{
		return $this->belongsTo('App\Http\Models\CompanyAuth');
	}

	public static function hasClaimedOffer($p_ClientId, $p_OfferId)
	{
		$v_SessionLogs = PointsLog::where('client_id', '=', $p_ClientId)
		                          ->where('offer_id', '=', $p_OfferId)
		                          ->first();
		return $v_SessionLogs != null;
	}

	public static function offerLog($p_ClientId, $p_CompanyId, $p_CompanyAuthId, $p_OfferId, $p_Price, $p_Points, $p_GenerateBonusPoints)
	{
		$v_PointsLog = new PointsLog();
		$v_PointsLog->client_id = $p_ClientId;
		$v_PointsLog->company_id = $p_CompanyId;
		$v_PointsLog->company_auth_id = $p_CompanyAuthId;
		$v_PointsLog->offer_id = $p_OfferId;
		$v_PointsLog->date = Carbon::now()->format('Y-m-d H:i:s');
		$v_PointsLog->price = $p_Price;
		$v_PointsLog->value = $p_Points;
		if($p_GenerateBonusPoints == true)
			$v_PointsLog->bonus_points = $p_Price * Parameters::getBonusPointsRate();
		$v_PointsLog->save();
	}

	public static function prizeLog($p_ClientId, $p_CompanyId, $p_CompanyAuthId, $p_OfferId, $p_Price, $p_Points)
	{
		$v_PointsLog = new PointsLog();
		$v_PointsLog->client_id = $p_ClientId;
		$v_PointsLog->company_id = $p_CompanyId;
		$v_PointsLog->company_auth_id = $p_CompanyAuthId;
		$v_PointsLog->offer_id = $p_OfferId;
		$v_PointsLog->date = Carbon::now()->format('Y-m-d H:i:s');
		$v_PointsLog->price = $p_Price;
		$v_PointsLog->value = -$p_Points;
		$v_PointsLog->save();
	}

	public static function getDTPointsLog($p_ClientName, $p_Date, $p_Type, $p_OfferID, $p_Order, $p_Start, $p_Length, $p_Draw, $p_ClientID, $p_CompanyID)
	{
        $v_Query = null;
        if($p_ClientID != null)
            $v_Query = PointsLog::where('client_id', $p_ClientID)->with('company', 'offer', 'companyAuth.address', 'client');
        else if($p_CompanyID != null)
            $v_Query = PointsLog::where('company_id', $p_CompanyID)->with('company', 'offer', 'companyAuth.address', 'client');

        if($p_ClientName != '')
		{
			$v_Query->whereHas('client', function($p_Query) use($p_ClientName)
			{
				$p_Query->where('name', 'LIKE', '%' . $p_ClientName . '%');
			});
		}
		if($p_Date != '')
		{
			$v_StartDate = Carbon::createFromFormat('d/m/Y', substr($p_Date, 0, 10));
			$v_Query->where('date', '>=', $v_StartDate->startOfDay()->format('Y-m-d H:i:s'));
			$v_EndDate = Carbon::createFromFormat('d/m/Y', substr($p_Date, 13, 23));
			$v_Query->where('date', '<=', $v_EndDate->endOfDay()->format('Y-m-d H:i:s'));
		}
		if($p_Type != '')
		{
//			if($p_Type == 'check-in')
//				$v_Query->where('is_checkin', '=', 1);
//			else if($p_Type == 'check-out')
//				$v_Query->where('is_checkin', '=', 0);
//			else
				if($p_Type == 'oferta' || $p_Type == 'recompensa') {
				$v_Query->whereHas('offer', function($p_Query) use($p_Type)
				{
					$p_Query->where('is_prize', '=', $p_Type == 'oferta' ? 0 : 1);
				});
			}
		}
		if($p_OfferID != '')
		{
			$v_Query->where('offer_id', '=', $p_OfferID);
		}

		if($p_Order != null)
		{
            if(BaseController::isAdmin())
            {
                if($p_Order["column"] == 1)
                    $v_Query->orderBy('date', $p_Order["dir"]);
                else if($p_Order["column"] == 3)
                    $v_Query->orderBy('offer_id', $p_Order["dir"]);
            }
            else
            {
                if($p_Order["column"] == 0)
                    $v_Query->orderBy('date', $p_Order["dir"]);
                else if($p_Order["column"] == 2)
                    $v_Query->orderBy('offer_id', $p_Order["dir"]);
            }
		}

		$v_QueryRes = $v_Query->get()->toArray();
		$v_Data = [];
		for($c_Index = 0 ; $c_Index < sizeof($v_QueryRes) ; $c_Index++)
		{
            $v_Date = Carbon::createFromFormat('Y-m-d H:i:s', $v_QueryRes[$c_Index]["date"])->format('d/m/y H:i');
            if (BaseController::isAdmin())
            {
//                $v_UnityName = '';
//                if ($v_QueryRes[$c_Index]['checked_out'] == 1)
//                    $v_UnityName = $v_QueryRes[$c_Index]["company_auth"]["address"]["name"];

                array_push($v_Data,
                    [
                        $v_QueryRes[$c_Index]["client"]["name"],
                        $v_Date,
                        $v_QueryRes[$c_Index]['offer_id'] == null ? ($v_QueryRes[$c_Index]['is_checkin'] ? "Check-in" : "Check-out") : ($v_QueryRes[$c_Index]['offer']['is_prize'] == 0 ? "Oferta" : "Recompensa"),
                        $v_QueryRes[$c_Index]['offer_id'],
                        $v_QueryRes[$c_Index]['price'] > 0 ? 'R$' . str_replace('.', ',', $v_QueryRes[$c_Index]['price']) : '',
	                    str_replace('.', ',', $v_QueryRes[$c_Index]['value']),
	                    str_replace('.', ',', $v_QueryRes[$c_Index]['bonus_points']),
	                    $v_QueryRes[$c_Index]["company_auth"]["name"]
                    ]);
            }
            else
            {
                array_push($v_Data,
                    [
	                    $v_QueryRes[$c_Index]["client"]["name"],
                        $v_Date,
                        $v_QueryRes[$c_Index]['offer_id'] == null ? ($v_QueryRes[$c_Index]['is_checkin'] ? "Check-in" : "Check-out") : ($v_QueryRes[$c_Index]['offer']['is_prize'] == 0 ? "Oferta" : "Recompensa"),
                        $v_QueryRes[$c_Index]['offer_id'],
                        $v_QueryRes[$c_Index]['price'] > 0 ? 'R$' . str_replace('.', ',', $v_QueryRes[$c_Index]['price']) : '',
                        $v_QueryRes[$c_Index]['value'],
	                    str_replace('.', ',', $v_QueryRes[$c_Index]['value']),
                        $v_QueryRes[$c_Index]["company_auth"]["name"]
                    ]);
            }
		}
		if($p_Length != -1)
			$v_Data = array_slice($v_Data, $p_Start, $p_Length);

		$v_DataTableAjax = new \stdClass();
		$v_DataTableAjax->draw = $p_Draw;
		$v_DataTableAjax->recordsTotal = sizeof($v_QueryRes);
		$v_DataTableAjax->recordsFiltered = sizeof($v_QueryRes);
		$v_DataTableAjax->data = $v_Data;
		return json_encode($v_DataTableAjax);
	}

    public static function getDTOfferStatistics($p_OfferID, $p_ClientName, $p_Date, $p_CompanyAuth, $p_Order, $p_Start, $p_Length, $p_Draw)
    {
        $v_Query = PointsLog::where('offer_id', $p_OfferID)->with('client', 'companyAuth');
        if($p_ClientName != '')
        {
            $v_Query->whereHas('client', function($p_Query) use($p_ClientName)
            {
                $p_Query->where('name', 'LIKE', '%' . $p_ClientName . '%');
            });
        }
        if($p_Date != '')
        {
            $v_StartDate = Carbon::createFromFormat('d/m/Y', substr($p_Date, 0, 10));
            $v_Query->where('date', '>=', $v_StartDate->startOfDay()->format('Y-m-d H:i:s'));
            $v_EndDate = Carbon::createFromFormat('d/m/Y', substr($p_Date, 13, 23));
            $v_Query->where('date', '<=', $v_EndDate->endOfDay()->format('Y-m-d H:i:s'));
        }
        if($p_CompanyAuth != '')
        {
            $v_Query->where('company_auth_id', '=', $p_CompanyAuth);
        }

        if($p_Order != null)
        {
            if($p_Order["column"] == 1)
                $v_Query->orderBy('date', $p_Order["dir"]);
            else if($p_Order["column"] == 3)
                $v_Query->orderBy('offer_id', $p_Order["dir"]);
            else if($p_Order["column"] == 4)
                $v_Query->orderBy('value', $p_Order["dir"]);
        }

        $v_QueryRes = $v_Query->get()->toArray();
        $v_Data = [];
        for($c_Index = 0 ; $c_Index < sizeof($v_QueryRes) ; $c_Index++)
        {
            $v_Date = Carbon::createFromFormat('Y-m-d H:i:s', $v_QueryRes[$c_Index]["date"])->format('d/m/y H:i');
            if (BaseController::isAdmin())
            {
                array_push($v_Data,
                    [
                        $v_QueryRes[$c_Index]["client"]["name"],
                        $v_Date,
                        $v_QueryRes[$c_Index]["company_auth"]["name"],
	                    str_replace('.', ',', $v_QueryRes[$c_Index]['bonus_points'])
                    ]);
            }
            else
            {
                array_push($v_Data,
                    [
                        $v_QueryRes[$c_Index]["client"]["name"],
                        $v_Date,
                        $v_QueryRes[$c_Index]["company_auth"]["name"]
                    ]);
            }
        }
        if($p_Length != -1)
            $v_Data = array_slice($v_Data, $p_Start, $p_Length);

        $v_DataTableAjax = new \stdClass();
        $v_DataTableAjax->draw = $p_Draw;
        $v_DataTableAjax->recordsTotal = sizeof($v_QueryRes);
        $v_DataTableAjax->recordsFiltered = sizeof($v_QueryRes);
        $v_DataTableAjax->data = $v_Data;
        return json_encode($v_DataTableAjax);
    }

    public static function getPointsLog($p_Amount, $p_Offset)
    {
	    return PointsLog::join('company', 'company.id', '=', 'points_log.company_id')
	                    ->where('points_log.client_id', Auth::guard('client')->id())
	                    ->orderBy('points_log.date', 'DESC')
	                    ->select('points_log.date', 'points_log.value', 'points_log.bonus_points', 'points_log.company_id', 'company.trade_name')
	                    ->skip($p_Offset)
	                    ->take($p_Amount)
	                    ->get();
    }
}
