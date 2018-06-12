<?php

namespace App\Http\Controllers;

use App\Http\Models\Company;
use App\Http\Models\Offer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OfferController extends BaseController
	{
		public function postOffer()
		{
			$v_OfferId = Input::get('offer_id');
			if($v_OfferId == null)
				$v_Validator = Validator::make(Input::all(), Offer::$addRules);
			else
				$v_Validator = Validator::make(Input::all(), Offer::$editRules);
			if (!$v_Validator->passes())
				return redirect()->back()
				               ->with('error_message', 'Os seguintes erros foram encontrados')
				               ->withErrors($v_Validator)
				               ->withInput();
			try
			{
				DB::beginTransaction();
				$v_Offer = $v_OfferId == null ? null : Offer::find($v_OfferId);
				if($v_OfferId == null || ($v_OfferId != null && $v_Offer->isEditable()))
				{
					if ($this->isAdmin())
						$v_CompanyId = Input::get('company_id');
					else
						$v_CompanyId = Auth::guard('company')->id();
					$v_Title = Input::get('title');
					$v_Points = Input::get('points');
					$v_Price = Input::get('price');
					$v_OldPrice = Input::get('old_price') == null ? 0 : Input::get('old_price');
					$v_Description = Input::get('description');
					$v_ActivationDate = Carbon::createFromFormat('d/m/Y', Input::get('activation_date'));
					$v_ActivationDate = $v_ActivationDate->format('Y-m-d H:i:s');
					$v_ValidUntil = Carbon::createFromFormat('d/m/Y', Input::get('valid_until'));
					$v_ValidUntil = $v_ValidUntil->format('Y-m-d H:i:s');
					$v_AmountAllowed = Input::get('amount_allowed');
					$v_AmountUsed = Input::get('amount_used');
					$v_IsPrize = Input::get('is_prize');
					$v_EnabledAt = 0;
					for($c_DayCounter = 0 ; $c_DayCounter < 7 ; $c_DayCounter++)
					{
						$v_DayLunch = Input::get('day_' . $c_DayCounter . '_lunch');
						if ( $v_DayLunch != null && $v_DayLunch == 'on')
							$v_EnabledAt |= (0x01 << (2 * $c_DayCounter));
						$v_DayDinner = Input::get('day_' . $c_DayCounter . '_dinner');
						if ( $v_DayDinner != null && $v_DayDinner == 'on')
							$v_EnabledAt |= (0x01 << ((2 * $c_DayCounter) + 1));
					}
					if($v_OfferId == null)
						$v_OfferId = Offer::createOffer($v_CompanyId, $v_Title, $v_Points, $v_Price, $v_OldPrice, $v_Description, $v_EnabledAt,
							$v_ActivationDate, $v_ValidUntil, $v_AmountAllowed, $v_IsPrize);
					else
						Offer::editOffer($v_OfferId, $v_CompanyId, $v_Title, $v_Points, $v_Price, $v_OldPrice, $v_Description, $v_EnabledAt,
							$v_ActivationDate, $v_ValidUntil, $v_AmountAllowed, $v_AmountUsed, $v_IsPrize);

					if (Input::hasFile('image_file') && Input::file('image_file')->isValid())
					{
						$v_Offer = Offer::find($v_OfferId);
						$v_File = Input::file('image_file');

	                    $v_Path = public_path() . '/images/offers/';
	                    if (!\File::exists($v_Path))
	                        \File::makeDirectory($v_Path, 493, true);
	                    if ($v_Offer->photo_url != null)
	                    {
	                        $v_OldFileName = explode('/', $v_Offer->photo_url);
	                        $v_OldFileName = array_pop($v_OldFileName);
	                        \File::delete($v_Path . $v_OldFileName);
	                    }                    
	                    $v_Img = Image::make($v_File);

	                    $v_Img->widen(320, function ($constraint){
	                        $constraint->upsize();
	                    });
	                    $v_Width = $v_Img->width();
	                    $v_Height = $v_Img->height();
	                    if($v_Width != $v_Height)
	                    {
	                        $v_Size = $v_Width < $v_Height ? $v_Width : $v_Height;
	                        $v_Img->crop($v_Size, $v_Size, floor(($v_Width - $v_Size)/2), floor(($v_Height - $v_Size)/2));
	                    }
	                    $v_ImgName =  time() . str_random(10);
	                    $v_Img->encode('jpg')->save($v_Path . $v_ImgName . '.jpg');
	                    $v_Img->resize(50, 50)->save($v_Path . $v_ImgName . 's.jpg');
	                    $v_Offer->photo_url = url('/images/offers/' . $v_ImgName);
	                    $v_Offer->save();
					}
					else if($v_OfferId == null)
						return redirect()->back()
						               ->with('error_message', 'Inserir uma imagem é obrigatório!')
						               ->withInput();

				}
				$v_Rule = Input::get('rule');
				Offer::createOfferRule($v_OfferId, $v_Rule);
				DB::commit();
				return redirect(url('/company/' . (Input::get('is_prize') ? 'prize' : 'offer') . 's/' . Input::get('company_id')))
				               ->with('message', 'As alterações foram salvas com sucesso!');
			}
			catch(\Exception $p_Ex)
			{
				DB::rollback();
				return redirect()->back()
				               ->with('error_message', 'Erro: ' . $p_Ex->getMessage())
				               ->withInput();
			}
		}

		public function add($p_Type, $p_CompanyId)
		{
			return view('offer.edit', array('p_Add' => $p_Type,
											      'p_Offer' => null,
											      'p_CompanyId' => $p_CompanyId,
											      'p_CantEdit' => false,
											      'p_CanEditRule' => true,
											      'p_IsPrize' => ($p_Type == 'prize' ? 1 : 0)));
		}

		public function edit($p_OfferId)
		{
			if($this->isAdmin())
				$v_Offer = Offer::find($p_OfferId);
			else
				$v_Offer = Offer::getFromCompany($p_OfferId);
			if($v_Offer == null)
				return redirect()->back()->with('error_message', 'Oferta não existe!');
			return view('offer.edit', array('p_Add' => false,
			                                      'p_Offer' => $v_Offer,
			                                      'p_CompanyId' => $v_Offer->company_id,
			                                      'p_CantEdit' => !$v_Offer->isEditable(),
			                                      'p_CanEditRule' => $v_Offer->isRuleEditable(),
			                                      'p_IsPrize' => $v_Offer->is_prize == 1));
		}

		public function deactivate($p_OfferId)
		{
			if($this->isCompany())
				$v_Offer = Offer::getActiveFromCompany($p_OfferId, Auth::guard('company')->id());
			else
				$v_Offer = Offer::getActive($p_OfferId);
//			return var_dump(DB::getQueryLog());
			if($v_Offer == null)
				return redirect()->back()->with('error_message', 'A oferta não existe ou não está ativa');
			try
			{
				Offer::deactivate($p_OfferId);
				return redirect()->back()->with('message', 'Oferta desativada com sucesso.');
			}
			catch(Exception $p_Ex)
			{
				return redirect()->back()->with('error_message', 'Erro ao desativar oferta.');
			}
		}

		public function get()
		{
			$v_Validator = Validator::make(Input::all(), Offer::$rulesGetOffers);
			if (!$v_Validator->passes())
				throw new BadRequestHttpException($v_Validator->errors()->all()[0]);

            $v_CompanyId = Input::get('company_id');
            $v_IsPrize = Input::get('is_prize');

            if($v_CompanyId == null)
                throw new BadRequestHttpException('no company_id provided');
            else if ($v_IsPrize == null)
                throw new BadRequestHttpException('no is_prize provided');
            else
                return Company::with(array('offers' => function($p_Query) use($v_IsPrize)
                {
                    $p_Query->where('is_prize', '=', $v_IsPrize)->orderBy('id', 'desc');
                }))->find($v_CompanyId);
		}
		
		public function getOfferListLayout($p_CompanyId = null)
		{
            if($this->isCompany() || $p_CompanyId == null)
                $v_Company = Company::with('offers')->find(Auth::guard('company')->id());
            else
            $v_Company = Company::with('offers')->find($p_CompanyId);
            return view('offer.list', array('p_Company' => $v_Company, 'p_IsPrize' => "0"));
        }
		
		public function getPrizeListLayout($p_CompanyId = null)
		{
            if($this->isCompany() || $p_CompanyId == null)
                $v_Company = Company::with('offers')->find(Auth::guard('company')->id());
            else
                $v_Company = Company::with('offers')->find($p_CompanyId);
            return view('offer.list', array('p_Company' => $v_Company, 'p_IsPrize' => "1"));
        }

		public function getStatistics($p_OfferId)
		{
			$v_OfferQuery = Offer::with('pointsLog.client')->where('id', '=', $p_OfferId);
			if($this->isAdmin())
			{
				$v_Offer = $v_OfferQuery->with('company')->first();
				if($v_Offer == null)
					return redirect(url('company/offers'))->with('message', 'A oferta não existe.');
			}
			else
			{
				$v_Offer = $v_OfferQuery->where('company_id', '=', Auth::guard('company')->id())->first();
				if($v_Offer == null)
					return redirect(url('company/offers'))->with('error_message', 'A oferta não existe ou pertence a outro estabelecimento.');
			}
			return view('offer.statistics', array('p_PointsLog' => $v_Offer->pointsLog, 'p_Offer' => $v_Offer));
		}

		public function getBonusPrizes() {
			return Offer::getBonusPrizes();
		}

		public function claimOffer() {
			$v_OfferId = Input::get('id');
			$v_Password = Input::get('company_auth_password');
			Offer::claimOffer($v_OfferId, $v_Password);
		}

		public function getOffers() {
			$v_CompanyTypeId = Input::get('company_type_id');
			$v_Amount = Input::get('amount');
			$v_Offset = Input::get('offset');
			return Offer::getOffers($v_CompanyTypeId, $v_Offset, $v_Amount, Auth::guard('client')->id());
		}
	}

?>
