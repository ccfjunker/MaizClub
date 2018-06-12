@extends('main')
@section('panel-header')
    {{ $p_Contact == null ? "Novo contato" : $p_Contact['name'] }}
@stop
@section('content')
    <div class="position-center">
        {{ Form::open(array('id' => 'mainForm', 'url'=>url('/company/editContact'), 'onsubmit' => 'return validateForm()')) }}
        @if($p_Contact != null)
            {{ Form::input('hidden', 'id', $p_Contact['id']) }}
        @endif
        {{ Form::input('hidden', 'company_id', \App\Http\Controllers\BaseController::isCompany() ? Auth::guard('company')->id() : ($p_Contact == null ? $p_CompanyId : $p_Contact['company_id'])) }}
        <div class="form-group">
            <label for="name">Nome</label>
            {{ Form::text('name', $p_Contact['name'], array('class'=>'form-control')) }}
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
           {{ Form::text('email', $p_Contact['email'], array('class'=>'form-control')) }}
        </div>
        <div class="form-group">
            <label for="cpf">CPF</label>
           {{ Form::text('cpf', $p_Contact['cpf'], array('id' => 'cpf', 'class'=>'form-control')) }}
        </div>
        <button id="back" type="button" style="margin-right: 5px;" class="btn btn-round btn-default pull-left" onclick="window.history.back()">Voltar</button>
        {{ Form::submit('Salvar', array('class'=>'btn btn-round btn-primary pull-right'))}}
        {{ Form::close() }}
    </div>
@stop
@section('pageScript')
<script type="text/javascript">
	function validateForm()
	{
        $('#cpf').mask('99999999999');
	    var v_Form = document.forms['mainForm'];

	    var v_Name = v_Form['name'].value;
	    if(v_Name == null || v_Name == "")
	    {
	        alert("Preencha o nome do contato.");
	        return false;
	    }

	    var v_Email = v_Form['email'].value;
	    if(v_Email == null || v_Email == "")
	    {
	        alert("Preencha o email do contato.");
	        return false;
	    }

	    var v_Cpf = v_Form['cpf'].value;
	    if(v_Cpf == null || v_Cpf == "")
	    {
	        alert("Preencha o cpf do contato.");
            $('#cpf').mask('999.999.999-99');
	        return false;
	    }
        if(!validateCPF(v_Cpf)) {
            alert('CPF inválido!');
            $('#cpf').mask('999.999.999-99');
            return false;
        }
	}

    function validateCPF(p_Cpf)
    {
        p_Cpf = p_Cpf.replace(/[^\d]+/g,'');
        if(p_Cpf == '')
            return false;
        if (p_Cpf.length != 11 || p_Cpf == "00000000000" || p_Cpf == "11111111111" || p_Cpf == "22222222222" || p_Cpf == "33333333333" || p_Cpf == "44444444444" ||
                p_Cpf == "55555555555" || p_Cpf == "66666666666" || p_Cpf == "77777777777" || p_Cpf == "88888888888" || p_Cpf == "99999999999")
            return false;
        var v_Add = 0;
        for (i=0; i < 9; i ++)
            v_Add += parseInt(p_Cpf.charAt(i)) * (10 - i);
        var v_Rev = 11 - (v_Add % 11);
        if (v_Rev == 10 || v_Rev == 11)
            v_Rev = 0;
        if (v_Rev != parseInt(p_Cpf.charAt(9)))
            return false;
        v_Add = 0;
        for (i = 0; i < 10; i ++)
            v_Add += parseInt(p_Cpf.charAt(i)) * (11 - i);
        v_Rev = 11 - (v_Add % 11);
        if (v_Rev == 10 || v_Rev == 11)
            v_Rev = 0;
        if (v_Rev != parseInt(p_Cpf.charAt(10)))
            return false;
        return true;
    }

	jQuery(function($)
    {
        $('#cpf').mask('999.999.999-99');
    });
</script>
<script src="{{url('js/jquery.maskedinput.js')}}"></script>
@stop