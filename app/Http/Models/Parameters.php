<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
class Parameters extends Model
{
	protected $table = 'parameters';

	public $timestamps = false;


	private static $m_CheckinPoints = null;
	private static  $m_MinCheckingDiff = null;
	private static  $m_MaxCheckinCheckoutDiff = null;
	private static  $m_PointsPerMoney = null;
	private static  $m_BonusPointsRate = null;
	private static  $m_PathToPublicFolder = null;

	public static function resetParameters()
	{
		Parameters::$m_CheckinPoints = null;
		Parameters::$m_MinCheckingDiff = null;
		Parameters::$m_MaxCheckinCheckoutDiff = null;
		Parameters::$m_PointsPerMoney = null;
		Parameters::$m_BonusPointsRate = null;
		Parameters::$m_PathToPublicFolder = null;
	}

	public static function getCheckinPoints()
	{
		if(Parameters::$m_CheckinPoints == null)
		{
			$v_CheckinPoints = Parameters::where('name', '=', 'checkin_pts')->first();
			if ($v_CheckinPoints != null)
				Parameters::$m_CheckinPoints = intval($v_CheckinPoints->value);
		}
		return Parameters::$m_CheckinPoints;
	}

	public static function getMinCheckinDiff()
	{
		if(Parameters::$m_MinCheckingDiff == null)
		{
			$v_CheckinDiff = Parameters::where('name', '=', 'checkin_diff')->first();
			if ($v_CheckinDiff != null)
				Parameters::$m_MinCheckingDiff = intval($v_CheckinDiff->value);
		}
		return Parameters::$m_MinCheckingDiff;
	}

	public static function getMaxCheckinCheckoutDiff()
	{
		if(Parameters::$m_MaxCheckinCheckoutDiff == null)
		{
			$v_Diff = Parameters::where('name', '=', 'checkin_checkout_diff')->first();
			if ($v_Diff != null)
				Parameters::$m_MaxCheckinCheckoutDiff = intval($v_Diff->value);
		}
		return Parameters::$m_MaxCheckinCheckoutDiff;
	}

	public static function getPointsPerMoney()
	{
		if(Parameters::$m_PointsPerMoney == null)
		{
			$v_Diff = Parameters::where('name', '=', 'point_per_money')->first();
			if ($v_Diff != null)
				Parameters::$m_PointsPerMoney = floatval($v_Diff->value);
		}
		return Parameters::$m_PointsPerMoney;
	}

	public static function getBonusPointsRate()
	{
		if(Parameters::$m_BonusPointsRate == null)
		{
			$v_Diff = Parameters::where('name', '=', 'bonus_points_rate')->first();
			if ($v_Diff != null)
				Parameters::$m_BonusPointsRate = floatval($v_Diff->value);
		}
		return Parameters::$m_BonusPointsRate;
	}

	public static function getPathToPublicFolder()
	{
		if(Parameters::$m_PathToPublicFolder == null)
		{
			$v_Diff = Parameters::where('name', '=', 'path_to_public_folder')->first();
			if ($v_Diff != null)
				Parameters::$m_PathToPublicFolder = $v_Diff->value;
		}
		return Parameters::$m_PathToPublicFolder;
	}

	public static function getUsageTerms()
	{
		$v_Param = Parameters::where('name', '=', 'usage_terms')->first();
		return $v_Param->value;
	}

	public static function getHelp()
	{
		$v_Param = Parameters::where('name', '=', 'help')->first();
		return $v_Param->value;
	}

	public static function getHelpItems()
	{
		return Parameters::where('name', '=', 'help')->get();
	}
}
