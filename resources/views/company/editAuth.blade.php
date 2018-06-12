@extends('main')
@section('panel-header')
    {{ $p_Auth == null ? "Novo autenticador" : $p_Auth['name']}}
@stop
@section('content')
    <div class="position-center">
        {{ Form::open(array('id' => 'mainForm', 'url'=>url('/company/editAuth'), 'onsubmit' => 'return validateForm()')) }}
        @if($p_Auth != null)
            {{ Form::input('hidden', 'id', $p_Auth['id']) }}
        @endif
        {{ Form::input('hidden', 'company_id', \App\Http\Controllers\BaseController::isCompany() ? Auth::guard('company')->id() : $p_CompanyId) }}
        <div class="form-group">
            <label for="name">Nome</label>
            {{ Form::text('name', $p_Auth['name'], array('class'=>'form-control')) }}
        </div>
        <div class="form-group">
            <label for="address_id">Unidade</label>
            {{ Form::select('address_id', $p_Addresses, $p_Auth == null ? '' : $p_Auth->address->id, array('class'=>'form-control')) }}
        </div>
        <div class="form-group">
            <label for="password">Senha</label>
           {{ Form::text('password', $p_Auth['password'], array('class'=>'form-control')) }}
        </div>
        {{ Form::submit('Salvar', array('class'=>'btn btn-round btn-primary btn-primary pull-right'))}}
        <button id="back" type="button" style="margin-right: 5px;" class="btn btn-round btn-default" onclick="window.history.back()">Voltar</button>
        {{ Form::close() }}
    </div>
@stop
@section('pageScript')
<script type="text/javascript">
	function validateForm()
	{
	    var v_Form = document.forms['mainForm'];
	    var v_Name = v_Form['name'].value;
	    if(v_Name == null || v_Name == "")
	    {
	        alert("Preencha o nome do autenticador.");
	        return false;
	    }
	    var v_Pwd = v_Form['password'].value;
	    if(v_Pwd == null)
	    {
	        alert("Preencha a senha do autenticador.");
	        return false;
	    }
	    else if(v_Pwd.length != 6 || isNaN(v_Pwd))
	    {
	        alert("A senha do autenticador deve ter 6 números.");
	        return false;
	    }
	}
</script>
@stop