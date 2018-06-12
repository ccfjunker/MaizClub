<?php

namespace App\Http\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class Company extends Authenticatable
{
    protected $guard = "company";

	protected $table = 'company';

	protected $hidden = array('password', 'remember_token', 'created_at', 'updated_at');

	public function points()
	{
		return $this->hasMany('App\Http\Models\Points');
	}

	public function points_log()
	{
		return $this->hasMany('App\Http\Models\PointsLog');
	}

	public function offers()
	{
		return $this->hasMany('App\Http\Models\Offer');
	}
	
	public function company_type()
	{
		return $this->belongsTo('App\Http\Models\CompanyType');
	}

	public function company_contact()
	{
		return $this->hasMany('App\Http\Models\CompanyContact');
	}
	
	public function company_auth()
	{
		return $this->hasMany('App\Http\Models\CompanyAuth');
	}

	public function addresses()
	{
		return $this->hasMany('App\Http\Models\Address');
	}

	public function clients()
	{
		return $this->belongsToMany('App\Http\Models\Client', 'points');
	}
	
	public static $rules = array(
		'cnpj'=>'required|numeric|digits_between:14,15|unique:company',
		'trade_name'=>'required|min:1',
		'email'=>'required|email|unique:company',
		'company_type_id'=>'required|numeric',
		'image_file'=>'required|image',
		'image_outer_file'=>'required|image',
		'password'=>'required|min:6|confirmed',
		'company_name'=>'min:1',
		'select_city'=>'required|min:1',
		'select_state'=>'required|min:1',
		'cep'=>'required|numeric|digits:8',
		'street'=>'required|min:1',
		'street_number'=>'required|min:1',
		'neighborhood'=>'required|min:1',
		'complement'=>'min:1',
        'latitude'=>'required|numeric',
        'longitude'=>'required|numeric',
		'tel'=>'numeric|digits_between:10,11',
		'description'=>'min:1',
		'country'=>'required|min:1',
		'lunch_start'=>'required|between:5,8',
		'lunch_end'=>'required|between:5,8',
		'dinner_start'=>'required|between:5,8',
		'dinner_end'=>'required|between:5,8'
	);

	public static $editRules = array(
		'cnpj'=>'required|numeric|digits_between:14,15|unique:company,cnpj,{id}',
		'trade_name'=>'required|min:1',
		'email'=>'required|email|unique:company,email,{id}',
		'company_type_id'=>'required|numeric',
		'image_file'=>'image',
		'image_outer_file'=>'image',
		'password'=>'required|min:6',
		'new_password'=>'min:6|confirmed',
		'company_name'=>'min:1',
		'description'=>'min:1',
		'lunch_start'=>'required|between:5,8',
		'lunch_end'=>'required|between:5,8',
		'dinner_start'=>'required|between:5,8',
		'dinner_end'=>'required|between:5,8'
	);

	public static function getEditRules($p_Id)
	{
		$v_Rules = array();
		foreach(Company::$editRules as $c_RuleId => $c_Rule)
			$v_Rules[$c_RuleId] = str_replace('{id}', $p_Id, $c_Rule);
		return $v_Rules;
	}

	public static $rulesDeactivate = array(
        'company_id'=>'required|numeric',
        'id'=>'required|numeric'
    );

    public static $rulesDelete = array(
        'company_id'=>'required|numeric',
        'id'=>'required|numeric'
    );

    public static function getDTCompanies($p_TradeName, $p_CNPJ, $p_Status, $p_Order, $p_Start, $p_Length, $p_Draw)
    {
        $v_Query = Company::with('offers');
        if($p_TradeName != '')
        {
            $v_Query->where('trade_name', 'LIKE', '%' . $p_TradeName . '%');
        }

        $v_UnwantedCharacters = array('.', '/', '-');
        if($p_CNPJ != '')
        {
            $v_Query->where('cnpj', 'LIKE', '%' . str_replace($v_UnwantedCharacters, '', $p_CNPJ) . '%');
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
                $v_Query->orderBy('trade_name', $p_Order["dir"]);
            else if($p_Order["column"] == 2)
                $v_Query->orderBy('cnpj', $p_Order["dir"]);
        }

        $v_QueryRes = $v_Query->get();
        $v_Data = [];
        $v_DataTableAjax = new \stdClass();
        $v_DataTableAjax->recordsTotal = sizeof($v_QueryRes);
        $v_DataTableAjax->recordsFiltered = sizeof($v_QueryRes);
        if($p_Length != -1)
            $v_QueryRes = $v_QueryRes->slice($p_Start, $p_Length);

        foreach($v_QueryRes as $c_Company)
        {
            $v_CNPJ = substr($c_Company->cnpj, 0, 2) . '.' . substr($c_Company->cnpj, 2, 3) . '.' . substr($c_Company->cnpj, 5, 3) . '/' . substr($c_Company->cnpj, 8, 4) . '-' . substr($c_Company->cnpj, 12, 2);
            $v_OfferDetails = Company::fillOfferDetails($c_Company->offers);
            $v_Actions = "<a style='margin-right: 5px;' title='Estatísticas' class='btn btn-primary' href='" . URL("/company/statistics") . '/' . $c_Company->id . "'><i class='ico-stats'></i></a>" .
                        "<a style='margin-right: 5px;' title='Editar' class='btn btn-primary' href='" . URL("/company/edit") . '/' . $c_Company->id . "'><i class='ico-pencil'></i></a>" .
                        "<a style='margin-right: 5px;' title='Contatos' class='btn btn-primary' href='" . URL("/company/contact") . '/' . $c_Company->id . "'><i class='fa fa-users'></i></a>" .
                        "<a style='margin-right: 5px;' title='Autenticadores' class='btn btn-primary' href='" . URL("/company/auth") . '/' . $c_Company->id . "'><i class='fa fa-key'></i></a>";
            array_push($v_Data,
                [
                    $c_Company->id,
                    $c_Company->trade_name,
                    $v_CNPJ,
                    '<i class="fa ' . ($c_Company->isActive() ? 'fa-check' : 'fa-times') . '"></i>',
                    $v_OfferDetails['offers'] . '&nbsp;<a style="margin-right: 5px;" title="Ofertas" class="btn btn-primary" href="' . URL("/company/offers") . '/' . $c_Company->id . '")><i class="fa fa-usd"></i></a>',
//                    $v_OfferDetails['prizes'] . '&nbsp;<a style="margin-right: 5px;" title="Recompensas" class="btn btn-primary" href="' . URL("/company/prizes") . '/' . $c_Company->id . '")><i class="fa fa-trophy"></i></a>',
                    $v_Actions]);
        }

        $v_DataTableAjax->draw = $p_Draw;
        $v_DataTableAjax->data = $v_Data;
        return json_encode($v_DataTableAjax);
    }

    private static function fillOfferDetails($p_Offers)
    {
        $v_TotalOffers = 0;
        $v_ActiveOffers = 0;
        $v_TotalPrizes = 0;
        $v_ActivePrizes = 0;
        for($c_Index = 0 ; $c_Index < sizeof($p_Offers) ; $c_Index++)
        {
            $v_IsActive = false;
            if ($p_Offers[$c_Index]['date_deactivated'] == null)
            {
                $v_ActivationDate = Carbon::createFromFormat('Y-m-d H:i:s', $p_Offers[$c_Index]['activation_date']);
                $v_ValidUntil = Carbon::createFromFormat('Y-m-d H:i:s', $p_Offers[$c_Index]['valid_until']);
                $v_Now = Carbon::now();
                if ($v_Now->lt($v_ValidUntil) || $v_ActivationDate->lt($v_Now))
                    $v_IsActive = true;
            }

            if ($p_Offers[$c_Index]['is_prize'])
            {
                $v_TotalPrizes++;
                if ($v_IsActive)
                    $v_ActivePrizes++;
            }
            else
            {
                $v_TotalOffers++;
                if ($v_IsActive)
                    $v_ActiveOffers++;
            }
        }
        $v_OfferDetails['offers'] = $v_ActiveOffers . '/' . $v_TotalOffers;
        $v_OfferDetails['prizes'] = $v_ActivePrizes . '/' . $v_TotalPrizes;
        return $v_OfferDetails;
    }

    public static function createCompany($p_CNPJ, $p_TradeName, $p_CompanyTypeId, $p_Email, $p_Pwd, $p_CompanyName, $p_Description, $p_LunchStart, $p_LunchEnd, $p_DinnerStart, $p_DinnerEnd, $p_LogoImg, $p_OuterImg)
    {
        $v_Company = new Company;
        $v_Company->cnpj = $p_CNPJ;
        $v_Company->trade_name = $p_TradeName;
        $v_Company->company_type_id = $p_CompanyTypeId;
        $v_Company->email = $p_Email;
        $v_Company->password = $p_Pwd;
        if ($p_CompanyName != NULL)
            $v_Company->company_name = $p_CompanyName;
        if ($p_Description != NULL)
            $v_Company->description = $p_Description;

        $v_Company->lunch_start = $p_LunchStart;
        $v_Company->lunch_end = $p_LunchEnd;
        $v_Company->dinner_start = $p_DinnerStart;
        $v_Company->dinner_end = $p_DinnerEnd;


        $v_URLs = Company::saveCompanyImages($v_Company, $p_LogoImg, $p_OuterImg);
        if($p_LogoImg != null)
            $v_Company->logo_url = url($v_URLs['logo']);
        if($p_OuterImg != null)
            $v_Company->photo_url = url($v_URLs['outer']);

        $v_Company->save();

        return $v_Company->id;
    }

    public static function updateCompany($p_Id, $p_CNPJ, $p_TradeName, $p_CompanyTypeId, $p_Email, $p_Pwd, $p_CompanyName, $p_Description, $p_LunchStart, $p_LunchEnd, $p_DinnerStart, $p_DinnerEnd, $p_LogoImg, $p_OuterImg)
    {
        $v_Company = Company::find($p_Id);
        if ($p_CNPJ != NULL)
            $v_Company->cnpj = $p_CNPJ;
        $v_Company->trade_name = $p_TradeName;
        $v_Company->company_type_id = $p_CompanyTypeId;
        $v_Company->email = $p_Email;
        if ($p_Pwd != NULL)
            $v_Company->password = $p_Pwd;
        if ($p_CompanyName != NULL)
            $v_Company->company_name = $p_CompanyName;
        if ($p_Description != NULL)
            $v_Company->description = $p_Description;

        $v_Company->lunch_start = $p_LunchStart;
        $v_Company->lunch_end = $p_LunchEnd;
        $v_Company->dinner_start = $p_DinnerStart;
        $v_Company->dinner_end = $p_DinnerEnd;

        $v_URLs = Company::saveCompanyImages($v_Company, $p_LogoImg, $p_OuterImg);
        if($p_LogoImg != null)
            $v_Company->logo_url = url($v_URLs['logo']);
        if($p_OuterImg != null)
            $v_Company->photo_url = url($v_URLs['outer']);

        $v_Company->save();

    }

    private static function saveCompanyImages($p_Company, $p_LogoImg, $p_OuterImg)
    {
        $v_URLs = [];
        if ($p_LogoImg != null)
        {
            $v_Path = public_path() . '/images/logos/';
            if (!\File::exists($v_Path))
                \File::makeDirectory($v_Path, 493, true);
        
            if ($p_Company->logo_url != null)
            {
                $v_OldFileName = explode('/', $p_Company->logo_url);
                $v_OldFileName = array_pop($v_OldFileName);
                \File::delete($v_Path . $v_OldFileName);
            }
            $v_Img = Image::make($p_LogoImg);
            $v_Img->widen(96, function ($constraint){
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
            $v_URLs['logo'] = '/images/logos/' . $v_ImgName;
        }

        if ($p_OuterImg != null)
        {

            $v_Path = public_path() . '/images/outer/';
            if (!\File::exists($v_Path))
                \File::makeDirectory($v_Path, 493, true);
        
            if ($p_Company->photo_url != null)
            {
                $v_OldFileName = explode('/', $p_Company->photo_url);
                $v_OldFileName = array_pop($v_OldFileName);
                \File::delete($v_Path . $v_OldFileName);
            }
            $v_Img = Image::make($p_OuterImg);
            $v_Img->widen(320, function ($constraint){
                $constraint->upsize();
            });
            $v_Width = $v_Img->width();
            $v_Height = $v_Img->height();

            if($v_Height > $v_Width)
            {
                $v_CropWidth = $v_Width;
                $v_CropHeight = $v_CropWidth * 2;
            }
            else
            {
                $v_CropHeight = $v_Height;
                $v_CropWidth = $v_CropHeight * 2;
            }

            $v_Img->crop(320, 160, floor((320 - $v_CropWidth)/2), floor((160 - $v_CropHeight)/2));

            $v_ImgName =  time() . str_random(10) . '.jpg';
            $v_Img->encode('jpg')->save($v_Path . $v_ImgName);
            $v_URLs['outer'] = '/images/outer/' . $v_ImgName;
        }
        return $v_URLs;
    }

    public function isActive()
    {
        return $this->deactivated == null;
    }

	private static $m_CompanySelectFields = ['company.id', 'company.email', 'company.description', 'company.open_time', 'company.close_time', 'company.lunch_start', 'company.lunch_end', 'company.dinner_start', 'company.dinner_end', 'company.logo_url', 'company.photo_url'];
	private static $m_AddressSelectFields = ['address.tel', 'address.city', 'address.state', 'address.country', 'address.street', 'address.neighborhood', 'address.complement', 'address.latitude', 'address.longitude'];
	private static $m_CompanyTypeSelectFields = ['company_type.name as company_type_name'];
	private static $m_StreetNumberField = 'IFNULL(address.street_number,0) as street_number';
	private static $m_PointsSelectFields = 'IFNULL(points.value,0) as points_value';
	private static $m_CompositeName = '(CASE address.name WHEN "" THEN company.trade_name ELSE CONCAT(company.trade_name, " - ", address.name) END) as trade_name';

	public static function getCompanies($p_Latitude, $p_Longitude, $p_TradeName, $p_CompanyTypeId, $p_Offset, $p_Amount, $p_ClientId)
	{
		$v_Select = array_merge(Company::$m_CompanySelectFields,
			Company::$m_AddressSelectFields,
			Company::$m_CompanyTypeSelectFields,
			[DB::raw(Company::$m_StreetNumberField)],
			[DB::raw(Company::$m_PointsSelectFields)],
			[DB::raw(Company::$m_CompositeName)]
        );
		$v_Query = Company::join('address', 'address.company_id', '=', 'company.id')
			->join('company_type', 'company.company_type_id', '=', 'company_type.id')
			->leftJoin('points', function($p_Join) use($p_ClientId)
			{
				$p_Join->on('company.id', '=', 'points.company_id')
					->where('points.client_id', '=', $p_ClientId);
			})
            ->with(['offers' => function($p_Query)
            {
                $p_Query->select(Offer::$m_OfferSelect)
                    ->join(DB::raw(Offer::$m_LastRuleJoin), 'offer.id', '=', 'last_rule.offer_id')
                    ->where('activation_date', '<=', Carbon::now()->endOfDay()->format('Y-m-d H:i:s'))
                    ->where('valid_until', '>=', Carbon::now()->startOfDay()->format('Y-m-d H:i:s'))
                    ->whereRaw('amount_allowed > amount_used')
                    ->whereNull('date_deactivated');
            }])
			->where('address.is_active', '=', 1)
			->where('company.id', '!=', 1);
		if($p_TradeName != null)
			$v_Query->where('trade_name', 'LIKE', '%' . $p_TradeName . '%');
		if($p_CompanyTypeId != null)
			$v_Query->where('company_type_id', '=', $p_CompanyTypeId);
		if($p_Offset != null)
			$v_Query->skip($p_Offset);
		if($p_Amount != null)
			$v_Query->take($p_Amount);
		if($p_Latitude != null && $p_Longitude != null)
		{
			$v_Query->whereNotNull('latitude')
				->whereNotNull('longitude')
				->select(array_merge($v_Select, [DB::raw( '6370.6934842*ACOS(SIN(RADIANS(' . $p_Latitude . '))*SIN(RADIANS(latitude))+COS(RADIANS(' . $p_Latitude . '))*COS(RADIANS(latitude))*COS(RADIANS(' . $p_Longitude . ' - longitude))) as dist')]))
		        ->orderBy('dist');
		}
		else
			$v_Query->select($v_Select)
				->orderBy('trade_name');

		return $v_Query->get();
	}

	public static function getCompaniesWithPoints($p_ClientId)
	{
		$m_FirstAddressJoin = '(select t1.* from address t1 join (select min(id) as id, company_id from address group by(company_id)) as t2 on t1.id = t2.id) as address';
		$v_Select = array_merge(Company::$m_CompanySelectFields,
			Company::$m_AddressSelectFields,
			Company::$m_CompanyTypeSelectFields,
			[DB::raw(Company::$m_PointsSelectFields)],
			['company.trade_name']);
        return Company::join(DB::raw($m_FirstAddressJoin), 'address.company_id', '=', 'company.id')
                      ->join('company_type', 'company.company_type_id', '=', 'company_type.id')
                      ->join('points', function($p_Join) use($p_ClientId)
                      {
                          $p_Join->on('company.id', '=', 'points.company_id')
                                 ->where('points.client_id', '=', $p_ClientId);
                      })
                      ->with(['offers' => function($p_Query)
                      {
                          $p_Query->select(Offer::$m_OfferSelect)
                                  ->join(DB::raw(Offer::$m_LastRuleJoin), 'offer.id', '=', 'last_rule.offer_id')
                                  ->where('activation_date', '<=', Carbon::now()->endOfDay()->format('Y-m-d H:i:s'))
                                  ->where('valid_until', '>=', Carbon::now()->startOfDay()->format('Y-m-d H:i:s'))
                                  ->whereNull('date_deactivated');
                      }])
                      ->where('is_active', '=', 1)
                      ->where('company.id', '!=', 1)
                      ->select($v_Select)
                      ->get();
	}
}
