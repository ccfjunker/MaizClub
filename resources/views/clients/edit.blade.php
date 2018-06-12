@extends('base')
@section('pageCSS')
<style type="text/css">
    a:hover
    {
        color: #000099;
        text-decoration: underline;
    }
    .btn-modal-popup:hover
    {
        text-decoration: none !important;
        color: #fff !important;
    }
</style>
@stop
@section('panel-header')
    @if($p_Client == null)
        Adicionar Usuário
    @else
        Dados do Usuário
    @endif
@stop
@section('mainContent')
<div class="row">
    <div class='col-sm-12'>
        <section class="panel">
            <header class="panel-heading">
                @if($p_Client == null)
                    Adicionar Usuário
                @else
                    Dados do Usuário
                @endif
            </header>
            <div class='panel-body'>
                @if(Session::has('message'))
                    <div class="alert alert-success alert-block fade in">
                        <p>{{ Session::get('message') }}</p>
                    </div>
                @endif
                @if(Session::has('error_message'))
                    <div class="alert alert-danger alert-block fade in">
                        <p>{{ Session::get('error_message') }}</p>
                    </div>
                @endif
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger alert-block fade in">
                        <p>{{ $error }}</p>
                    </div>
                @endforeach
                @if($p_Client == null)
                    {{ Form::open(array('url'=>url('/client/add'), 'onsubmit' => 'return onSubmitForm()', 'id' => 'mainForm')) }}
                @else
                    {{ Form::open(array('url'=>url('/client/update'), 'onsubmit' => 'return onSubmitForm()', 'id' => 'mainForm')) }}
                    {{ Form::input('hidden', 'client_id', $p_Client['id']) }}
                @endif
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="name">Nome</label>
                            {{ Form::text('name', $p_Client['name'], array('class'=>'form-control')) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="row">
                            <div class="{{\App\Http\Controllers\BaseController::isAdmin() && $p_Client != null ? 'col-xs-9' : 'col-xs-12'}}">
                                <div class="form-group">
                                    <label for="cpf">CPF</label>
                                    {{ Form::text('cpf', $p_Client['cpf'], array('id' => 'cpf', 'class'=>'form-control')) }}
                                </div>
                            </div>
                            @if(\App\Http\Controllers\BaseController::isAdmin() && $p_Client != null)
                                <div class="col-xs-3">
                                    <button id="status" type="button" class="btn btn-round btn-danger btn-deactivate" onclick="onClickChangeClientStatus()">{{ ($p_Client->deactivated == null) ? 'Desativar' : 'Ativar'}}</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            {{ Form::text('email', $p_Client['email'], array('class'=>'form-control')) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                    </div>
                </div>

                @if($p_Client == null)
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="password">Senha</label>
                                {{ Form::password('password', array('class'=>'form-control')) }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirmação da senha</label>
                                {{ Form::password('password_confirmation', array('class'=>'form-control')) }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="checkbox">
                        <label>
                            {{ Form::checkbox('changePassword', null, '0', array('onchange' => 'onChangeNewPwd()')) }}
                            Trocar senha
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="new_password">Nova senha</label>
                                {{ Form::password('new_password', array('class'=>'form-control newPassword', 'disabled'=> 'disabled')) }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="new_password_confirmation">Confirmação da nova senha</label>
                                {{ Form::password('new_password_confirmation', array('class'=>'form-control newPasswordConfirmation','disabled'=> 'disabled')) }}
                            </div>
                        </div>
                    </div>
                @endif
                @if($p_Client == null)
                    {{ Form::submit('Salvar', array('class'=>'btn btn-round btn-primary pull-right'))}}
                @else
                    <a id="save" href="#pwdModal" data-toggle="modal" class="btn btn-round btn-primary btn-modal-popup pull-right">Salvar</a>
                    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="pwdModal" class="modal fade" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                    <h4 class="modal-title">Confirmar operação</h4>
                                </div>
                                <div class="modal-body" style="display:inline">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            {{ Form::password('password', array('class'=>'form-control', 'id'=>'pwd', 'placeholder' => \App\Http\Controllers\BaseController::isAdmin() ? 'Senha do administrador' : 'Senha')) }}
                                            {{ Form::submit('Salvar', array('class'=>'btn btn-round btn-primary'))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <button id="back" type="button" style="margin-right: 5px;" class="btn btn-round btn-default" onclick="window.history.back()">Voltar</button>
                {{ Form::close() }}
            </div>
            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
        </section>
    </div>
</div>
@stop
@section('pageScript')
<script type="text/javascript">
@if($p_Client === null)
    $('form input, form select').keypress(function (event) {
        var keyCode = (event.keyCode ? event.keyCode : event.which);
		if (keyCode == '13') {
			event.preventDefault();
			$('input[type="submit"]').click();
		}
    });
@else
    $('form input:not(#pwd), form select').keypress(function (event) {
        var keyCode = (event.keyCode ? event.keyCode : event.which);
		if (keyCode == '13') {
			event.preventDefault();
			$('#save').click();
		}
    });
	$('#pwd').keypress(function (event) {
        var keyCode = (event.keyCode ? event.keyCode : event.which);
		if (keyCode == '13') {
			event.preventDefault();
			$('input[type="submit"]').click();
		}
    });

    function onChangeNewPwd()
    {
        $('.newPassword, .newPasswordConfirmation').attr('disabled', !$('.newPassword, .newPasswordConfirmation').attr('disabled'));
    }
@endif
    jQuery(function($)
    {
        $('#cpf').mask('999.999.999-99');
    });

    function onSubmitForm()
    {
        $('#cpf').mask('99999999999');
        var v_Form = document.forms['mainForm'];
        if(v_Form['name'].value === null || v_Form['name'].value.length < 2)
        {
            $('#cpf').mask('999.999.999-99');
			$('.modal button.close').click();
            alert('O campo de nome deve ser preenchido');
            return false;
        }
		else if(!validateCPF(v_Form['cpf'].value))
		{
			$('#cpf').mask('999.999.999-99');
			$('.modal button.close').click();
            alert('CPF inválido!');
            return false;
		}
        else if(v_Form['email'].value === null || v_Form['email'].value === '')
        {
            $('#cpf').mask('999.999.999-99');
			$('.modal button.close').click();
            alert('O campo de e-mail deve ser preenchido');
            return false;
        }
		else if(v_Form['password'].value === null || v_Form['password'].value === '')
        {
            $('#cpf').mask('999.999.999-99');
            alert('A senha é obrigatória!');
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
@if($p_Client !== null)
	function onClickChangeClientStatus()
	{
	    var v_DeactivateBtn = $('button#status');
		if (v_DeactivateBtn.text().match(/desativar/i))
			var v_ActivateAccount = 0;
		else
			var v_ActivateAccount = 1;
		var v_Xmlhttp = new XMLHttpRequest();
		v_Xmlhttp.onreadystatechange = function()
		{
			if (v_Xmlhttp.readyState == 4)
			{
				var v_Result = JSON.parse(v_Xmlhttp.responseText);
				if (v_Result['error'] == "ok")
					v_DeactivateBtn.text(v_ActivateAccount ? "Desativar" : "Ativar");
			}
		}
		v_Xmlhttp.open("GET","{{URL("/client/deactivate/" . $p_Client->id)}}", true);
		v_Xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		v_Xmlhttp.send();
	}
	function focusOnModal(){
        if($('#pwd').is(':visible')){
            $('#pwd').focus();
        }
	}
	window.setInterval(focusOnModal, 100);
	$(document).ready(function () {
		if($('input[name="changePassword"]').is(':checked'))
			$('.newPassword, .newPasswordConfirmation').attr('disabled', false);
	});
@endif
</script>
<script src="{{url('js/jquery.maskedinput.js')}}"></script>
@stop