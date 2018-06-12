<?php

namespace App\Http\Controllers;

use App\Http\Models\CompanyType;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CompanyTypeController extends BaseController {
    public function getCompanyTypes() {
        return CompanyType::where('id', '!=', 1)->where('is_active', 1)->select('id', 'name', 'photo_url')->get();
    }

    public function search()
    {
        return view('companyType.search')->with(['p_CompanyTypes' => CompanyType::get()]);
    }

    public function edit($p_Id = null)
    {
        $v_Type = $p_Id == null ? null : CompanyType::find($p_Id);
        return view('companyType.edit', array('p_Type' => $v_Type));
    }

    public function post()
    {
        $v_Id = Input::get('id');
        $v_Validator = Validator::make(Input::all(), $v_Id == null ? CompanyType::$rules : CompanyType::$editRules);
        if (!$v_Validator->passes()) {
            return redirect()->back()->with('error_message', 'Os seguintes erros foram encontrados')->withErrors($v_Validator)->withInput();
        }
        CompanyType::post($v_Id,
            Input::get('name'),
            (Input::hasFile('image_file') ? Input::file('image_file') : null)
        );

        return redirect(url('/companyType/search'))->with('message', 'O registro foi efetuado com sucesso.');
    }

    public function deactivate($p_Id)
    {
        $v_Type = CompanyType::find($p_Id);
        $v_Type->is_active = !$v_Type->is_active;
        $v_Type->save();

        return redirect(url('companyType/search'))->with('message', 'Alterações salvas!');
    }
}
?>
