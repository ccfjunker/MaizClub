<?php

namespace App\Http\Controllers;

use App\Http\Models\Company;
use App\Http\Models\CompanyAuth;
use App\Http\Models\CompanyContact;
use App\Http\Models\CompanyType;
use App\Http\Models\PointsLog;
use Carbon\Carbon;
use App\Http\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CompanyController extends BaseController {
    public function editCompany()
    {
        $v_IsNewCompany = (Input::get('id') == null ? 1 : 0);
        if ($v_IsNewCompany)
            $v_Validator = Validator::make(Input::all(), Company::$rules);
        else
            $v_Validator = Validator::make(Input::all(), Company::getEditRules(Input::get('id')));

        if ($v_Validator->passes())
        {
            if ($v_IsNewCompany)
                $v_Auth = true;
            else if($this->isAdmin() || $this->isCompany())
                $v_Auth = Auth::guard('company')->attempt(array('id'=>Auth::guard('company')->id(), 'password'=>Input::get('password')));
            else
                $v_Auth = false;
            if ($v_Auth)
            {
                if ($v_IsNewCompany)
                {
                    $v_CompanyId = Company::createCompany(
                        Input::get('cnpj'),
                        Input::get('trade_name'),
                        Input::get('company_type_id'),
                        Input::get('email'),
                        Hash::make(Input::get('password')),
                        '2114-06-10 12:00:00',
                        Input::get('company_name'),
                        Input::get('description'),
                        Input::get('lunch_start'),
                        Input::get('lunch_end'),
                        Input::get('dinner_start'),
                        Input::get('dinner_end'),
                        Input::file('image_file'),
                        Input::file('image_outer_file')
                    );
                    Address::createAddress(
                        $v_CompanyId,
                        '',
                        Input::get('tel'),
                        Input::get('select_city'),
                        Input::get('select_state'),
                        Input::get('country'),
                        Input::get('cep'),
                        Input::get('street'),
                        Input::get('street_number'),
                        Input::get('complement'),
                        Input::get('neighborhood'),
                        Input::get('latitude'),
                        Input::get('longitude')
                    );
                }
                else
                {
                    $v_CompanyId = Input::get('id');
                    $v_NewPassword = Input::get('new_password');
                    Company::updateCompany(
                        $v_CompanyId,
                        ($this->isAdmin() ? Input::get('cnpj') : null),
                        Input::get('trade_name'),
                        Input::get('company_type_id'),
                        Input::get('email'),
                        ($v_NewPassword == null ? null : Hash::make($v_NewPassword)),
                        Input::get('company_name'),
                        Input::get('description'),
                        Input::get('lunch_start'),
                        Input::get('lunch_end'),
                        Input::get('dinner_start'),
                        Input::get('dinner_end'),
                        (Input::hasFile('image_file') ? Input::file('image_file') : null),
                        (Input::hasFile('image_outer_file') ? Input::file('image_outer_file') : null)
                    );
                }
                if((BaseController::isAdmin() && $v_CompanyId == 1) || ( $v_CompanyId == Auth::guard('company')->id()))
                    return redirect(url('/company/edit'))->with('message', 'Edição Realizada!')
                                                        ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
                else
                    return redirect(url('/company/search'))->with('message', 'Edição Realizada!');
            }
            else
                return redirect(url('/company/edit/' . (Input::get('id') == null ? '' : Input::get('id'))))->with('error_message', 'Senha não confere!')->withInput();
        }
        else
        {
            if(Input::get('id') == null)
                $v_RedirectUrl = '/company/add/';
            else
                $v_RedirectUrl = '/company/edit/';

            return redirect(url($v_RedirectUrl . (Input::get('id') == null ? '' : Input::get('id'))))
                ->with('error_message', 'Os seguintes erros foram encontrados')
                ->withErrors($v_Validator)->withInput();
        }
    }

    public function editContact()
    {
        $v_ContactId = Input::get('id');
        if($v_ContactId == null)
            $v_Validator = Validator::make(Input::all(), CompanyContact::$rules);
        else
            $v_Validator = Validator::make(Input::all(), CompanyContact::getEditRules($v_ContactId));

        if ($v_Validator->passes())
        {
            if ($this->isAdmin() || (Auth::guard('company')->id() == Input::get('company_id')))
            {
                if($v_ContactId == null)
                    $v_Contact = new CompanyContact;
                else
                    $v_Contact = CompanyContact::find($v_ContactId);
                $v_Contact->company_id = Input::get('company_id');
                $v_Contact->name = Input::get('name');
                $v_Contact->email = Input::get('email');
                $v_Contact->cpf = Input::get('cpf');
                $v_Contact->save();

                return redirect(url('company/contact/' . Input::get('company_id')))
                    ->with('message', 'Alterações salvas!')
                    ->withInput();
            }
            else
                return redirect(url('company/' . Input::get('company_id') . '/editContact/' . Input::get('id')))
                    ->with('error_message', 'Você não tem permissão para realizar essa operação!')
                    ->withInput();
        }
        else
            return redirect(url('company/' . Input::get('company_id') . '/editContact/' . Input::get('id')))
                ->with('error_message', 'Os seguintes erros foram encontrados')
                ->withErrors($v_Validator)
                ->withInput();
    }

    public function deleteContact($p_CompanyId, $p_ContactId)
    {
        $v_Inputs = array('company_id' => $p_CompanyId, 'id' => $p_ContactId);
        $v_Validator = Validator::make($v_Inputs, Company::$rulesDelete);

        if ($v_Validator->passes())
        {
            if ($this->isAdmin() || (Auth::guard('company')->id() == $p_CompanyId))
            {
                $v_Contact = CompanyContact::find($p_ContactId);
                $v_Contact->delete();

                return redirect(url('company/contact/' . $p_CompanyId))->with('message', 'Alterações salvas!');
            }
            else
                return redirect(url('company/contact/' . $p_CompanyId))->with('error_message', 'Você não tem permissão para realizar essa operação!');
        }
        else
            return redirect(url('company/contact/' . $p_CompanyId))
                ->with('error_message', 'Os seguintes erros foram encontrados')
                ->withErrors($v_Validator)
                ->withInput();
    }

    public function editAuth()
    {
        $v_Validator = Validator::make(Input::all(), CompanyAuth::$rules);

        if ($v_Validator->passes())
        {
            if ($this->isAdmin() || (Auth::guard('company')->id() == Input::get('company_id')))
            {
                $v_AuthId = Input::get('id');
                if($v_AuthId == null)
                {
                    if (!CompanyAuth::createAuth(Input::get('company_id'), Input::get('password'), Input::get('address_id'), Input::get('name')))
                        return redirect(url('company/' . Input::get('company_id') . '/editAuth'))
                            ->with('error_message', 'Essa senha já está sendo utilizada!')
                            ->withInput();
                }
                else
                {
                    if (!CompanyAuth::updateAuth(Input::get('company_id'), Input::get('password'), Input::get('address_id'), Input::get('name'), $v_AuthId))
                        return redirect(url('company/' . Input::get('company_id') . '/editAuth'))
                            ->with('error_message', 'Essa senha já está sendo utilizada!')
                            ->withInput();
                }

                return redirect(url('company/auth/' . Input::get('company_id')))->with('message', 'Alterações salvas!');
            }
            else
                return redirect(url('company/auth/' . Input::get('company_id')))->with('error_message', 'Você não tem permissão para realizar essa operação!');
        }
        else
            return redirect(url('company/auth/' . Input::get('company_id')))
                ->with('error_message', 'Os seguintes erros foram encontrados')
                ->withErrors($v_Validator)
                ->withInput();
    }

    public function deactivateAuth($p_CompanyId, $p_AuthId)
    {
        $v_Inputs = array('company_id' => $p_CompanyId, 'id' => $p_AuthId);
        $v_Validator = Validator::make($v_Inputs, Company::$rulesDeactivate);

        if ($v_Validator->passes())
        {
            if ($this->isAdmin() || (Auth::guard('company')->id() == $p_CompanyId))
            {
                $v_Auth = CompanyAuth::find($p_AuthId);
                $v_Auth->is_active = !$v_Auth->is_active;
                $v_Auth->save();

                return redirect(url('company/auth/' . $p_CompanyId))->with('message', 'Alterações salvas!');
            }
            else
                return redirect(url('company/auth/' . $p_CompanyId))->with('error_message', 'Você não tem permissão para realizar essa operação!');
        }
        else
            return redirect(url('company/auth/' . $p_CompanyId))
                ->with('error_message', 'Os seguintes erros foram encontrados')
                ->withErrors($v_Validator)
                ->withInput();
    }

    public function deactivateCompany($p_CompanyId)
    {
        $v_Company = Company::find($p_CompanyId);

        if ($v_Company->deactivated == null)
            $v_Company->deactivated = Carbon::now()->format('Y-m-d H:i:s');
        else
            $v_Company->deactivated = null;
        $v_Company->save();

        $v_Error['error'] = 'ok';
        return json_encode($v_Error);
    }

    public function getAddCompanyLayout()
    {
        $v_CompanyTypes = CompanyType::where('is_active', 1)->lists('name','id');
        return view('company.edit', array('p_Company' => null, 'p_CompanyTypes' => $v_CompanyTypes));
    }

    public function getEditCompanyLayout($p_CompanyId = null)
    {
        if ($this->isCompany())
            $v_Company = Company::with('addresses')->find(Auth::guard('company')->id());
        else if ($this->isAdmin())
        {
            if ($p_CompanyId != null)
                $v_Company = Company::with('addresses')->find($p_CompanyId);
            else
                $v_Company = Company::with('addresses')->find(Auth::guard('company')->id());
        }
        $v_CompanyTypes = CompanyType::where('is_active', 1)->lists('name','id');
        return view('company.edit', array('p_Company' => $v_Company, 'p_CompanyTypes' => $v_CompanyTypes));
    }

    public function getContactCompanyLayout($p_CompanyId)
    {
        $v_CompanyId = $this->isAdmin() ? $p_CompanyId : Auth::guard('company')->id();
        $v_CompanyContact = CompanyContact::where('company_id', '=', $v_CompanyId)->get();
        return view('company.contactList', array('p_CompanyContact' => $v_CompanyContact, 'p_CompanyId' => $v_CompanyId));

    }

    public function getEditContact($p_CompanyId, $p_ContactId = null)
    {
        $v_Contact = $p_ContactId == null ? null : CompanyContact::find($p_ContactId);
        return view('company.editContact', array('p_Contact' => $v_Contact, 'p_CompanyId' => $p_CompanyId));
    }

    public function getAuthCompanyLayout($p_CompanyId = null)
    {
        $v_CompanyId = $this->isAdmin() ? $p_CompanyId : Auth::guard('company')->id();
        $v_CompanyAuth = CompanyAuth::getByCompany($v_CompanyId);
        return view('company.authList', array('p_CompanyAuth' => $v_CompanyAuth, 'p_CompanyId' => $v_CompanyId));
    }

    public function getEditAuth($p_CompanyId, $p_AuthId = null)
    {
        $v_Auth = CompanyAuth::with('address.company')->find($p_AuthId);
        if ($v_Auth != null && $v_Auth->is_active == 0)
            return redirect()->back()->with('error_message', 'Este autenticador não pode ser editado.');
        $v_AddressesList = [];
        $v_Addresses = Address::where('company_id', '=', $p_CompanyId)->get();
        for($c_Index = 0 ; $c_Index < $v_Addresses->count() ; $c_Index++)
        {
            $v_Addresses[$c_Index]->name = $v_Addresses[$c_Index]->company->trade_name . ($v_Addresses[$c_Index]->name == '' ? '' : ' - ' . $v_Addresses[$c_Index]->name);
            $v_AddressesList[$v_Addresses[$c_Index]->id] = $v_Addresses[$c_Index]->name;
        }
        return view('company.editAuth', array('p_Auth' => $v_Auth, 'p_CompanyId' => $p_CompanyId, 'p_Addresses' => $v_AddressesList));
    }

    public function searchCompany()
    {
        return view('company.search');
    }

    public function getStatistics($p_CompanyId)
    {
        $v_Company = Company::find($p_CompanyId);
        $v_PointsLog = PointsLog::with('offer', 'client')->where('company_id', '=', $p_CompanyId)->get();
        return view('company.statistics', array('p_PointsLog' => $v_PointsLog, 'p_Company' => $v_Company));
    }

    public function getCompanies() {
        $v_Latitude = Input::get('latitude');
        $v_Longitude = Input::get('longitude');
        $v_Amount = Input::get('amount');
        $v_Offset = Input::get('offset');
        $v_TradeName = Input::get('trade_name');
        $v_CompanyTypeId = Input::get('company_type_id');
        return Company::getCompanies($v_Latitude, $v_Longitude, $v_TradeName, $v_CompanyTypeId, $v_Offset, $v_Amount, Auth::guard('client')->id());
    }

    public function getWithPoints(){
        return Company::getCompaniesWithPoints(Auth::guard('client')->id());
    }

    public function getDTCompanies()
    {
        $v_Columns = Input::get('columns');
        $v_TradeName = $v_Columns[1]['search']['value'];
        $v_CNPJ = $v_Columns[2]['search']['value'];
        $v_Status = $v_Columns[3]['search']['value'];
        $v_Order = Input::get('order')[0];
        $v_Start = Input::get('start');
        $v_Length = Input::get('length');
        $v_Draw = Input::get('draw');
        return Company::getDTCompanies($v_TradeName, $v_CNPJ, $v_Status, $v_Order, $v_Start, $v_Length, $v_Draw);
    }

    public function addAddress($p_CompanyId = null)
    {
        if ($this->isAdmin() && $p_CompanyId != null)
            $v_CompanyId = $p_CompanyId;
        else
            $v_CompanyId = Auth::guard('company')->id();

        return view('company.editAddress', array('p_CompanyId' => $v_CompanyId, 'p_Address' => null));
    }

    public function editAddress($p_AddressId)
    {
        $v_Address = Address::find($p_AddressId);
        if ($this->isCompany() && $v_Address->company_id != Auth::guard('company')->id())
            return redirect()->back()->with('error_message', 'Acesso não autorizado.');

        return view('company.editAddress', array('p_CompanyId' => null, 'p_Address' => $v_Address));
    }

    public function postAddress()
    {
        $v_Validator = Validator::make(Input::all(), Address::$rules);
        if ($v_Validator->passes())
        {
            $v_AddressId = Input::get('id');
            if($v_AddressId == null)
                Address::createAddress(
                    Input::get('company_id'),
                    Input::get('name'),
                    Input::get('tel'),
                    Input::get('select_city'),
                    Input::get('select_state'),
                    Input::get('country'),
                    Input::get('cep'),
                    Input::get('street'),
                    Input::get('street_number'),
                    Input::get('complement'),
                    Input::get('neighborhood'),
                    Input::get('latitude'),
                    Input::get('longitude'));
            else
            {
                if(!$v_Address = Address::updateAddress(
                    $v_AddressId,
                    Input::get('name'),
                    Input::get('tel'),
                    Input::get('select_city'),
                    Input::get('select_state'),
                    Input::get('country'),
                    Input::get('cep'),
                    Input::get('street'),
                    Input::get('street_number'),
                    Input::get('complement'),
                    Input::get('neighborhood'),
                    Input::get('latitude'),
                    Input::get('longitude')))
                    return redirect()->back()->with('error_message', 'Esse endereço não existe.');
            }
            return redirect(url('/company/edit/' . Input::get('company_id')))->with('message', 'O endereço foi salvo com sucesso.');
        }
        else
        {
            return redirect()->back()
                ->with('error_message', 'Os seguintes erros foram encontrados')
                ->withErrors($v_Validator)
                ->withInput();
        }
    }

    public function deactivateAddress($p_AddressId)
    {
        $v_Address = Address::find($p_AddressId);
        if (Address::where('company_id', '=', $v_Address->company_id)->where('is_active', '=', 1)->count() == 1)
            return redirect()->back()
                ->with('error_message', 'O estabelecimento deve ter no mínimo um endereço ativo.')
                ->withInput();

        $v_Address->is_active = 0;
        $v_Address->save();

        return redirect(url('/company/edit/' . $v_Address->company_id))->with('message', 'O endereço foi desativado com sucesso.');
    }
}
?>
