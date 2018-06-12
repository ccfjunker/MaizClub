<?php

namespace App\Http\Controllers;

use App\Http\Models\PointsLog;
use Illuminate\Support\Facades\Input;

class PointsController extends BaseController
	{
        public function getDTPointsLog()
        {
            $v_ClientID = Input::get('p_ClientID');
            $v_CompanyID = Input::get('p_CompanyID');
            $v_Columns = Input::get('columns');

            $v_ClientName = $v_Columns[0]['search']['value'];
            $v_Date = $v_Columns[1]['search']['value'];
            $v_Type = $v_Columns[2]['search']['value'];
            $v_OfferID = $v_Columns[3]['search']['value'];

            $v_Order = Input::get('order')[0];
            $v_Start = Input::get('start');
            $v_Length = Input::get('length');
            $v_Draw = Input::get('draw');
            return PointsLog::getDTPointsLog($v_ClientName, $v_Date, $v_Type, $v_OfferID, $v_Order, $v_Start, $v_Length, $v_Draw, $v_ClientID, $v_CompanyID);
        }

        public function getDTOfferStatistics()
        {
            $v_OfferID = Input::get('p_OfferID');
            $v_Columns = Input::get('columns');
            $v_ClientName = $v_Columns[0]['search']['value'];
            $v_Date = $v_Columns[1]['search']['value'];
            $v_CompanyAuth = $v_Columns[2]['search']['value'];
            $v_Order = Input::get('order')[0];
            $v_Start = Input::get('start');
            $v_Length = Input::get('length');
            $v_Draw = Input::get('draw');
            return PointsLog::getDTOfferStatistics($v_OfferID, $v_ClientName, $v_Date, $v_CompanyAuth, $v_Order, $v_Start, $v_Length, $v_Draw);
        }

        public function getMobilePointsLog()
        {
            $v_Amount = Input::get('amount');
            $v_Offset = Input::get('offset');
            return PointsLog::getPointsLog($v_Amount, $v_Offset);
        }
	}
?>
