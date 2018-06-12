@extends('base')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTablesTemplate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/bootstrap-timepicker/css/timepicker.css')}}">
    <style type="text/css">
        a:hover
        {
            color: #000099;
            text-decoration: underline;
        }
        .dt-panelmenu, .dt-panelfooter
        {
            background: none;
        }
        #addresses-table
        {
            width: 99.9999% !important;
        }
        .table.dataTable th
        {
            min-width: 30px;
        }
        .table.dataTable .btn.btn-success
        {
            margin:0;
        }
        .dataTable tbody tr td
        {
            line-height: 34px;
        }
        .btn-modal-popup:hover
        {
            text-decoration: none !important;
            color: #fff !important;
        }
        img
        {
            margin-bottom: 5px;
        }
        textarea
        {
            resize:vertical;
        }
        .map-row
        {
            margin-bottom: 15px;
        }
    </style>
@stop
@section('mainContent')
<div class="row">
    <div class='col-sm-12'>
        <section class="panel">
            <header class="panel-heading">
                @if($p_Company === null)
                    Adicionar Estabelecimento
                @else
                    Editar Dados
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
                @if($p_Company === null)
                    {{ Form::open(array('url'=>URL('/company/add'), 'id' => 'mainForm', 'onsubmit' => 'return onSubmitForm()', 'files'=>true)) }}
                @else
                    {{ Form::open(array('url'=>URL('/company/update'), 'id' => 'mainForm', 'onsubmit' => 'return onSubmitForm()', 'files'=>true)) }}
                    {{ Form::input('hidden', 'id', $p_Company['id']) }}
                @endif
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="trade_name">Nome Fantasia</label>
                            {{ Form::text('trade_name', $p_Company['trade_name'], array('class'=>'form-control')) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        @if(\App\Http\Controllers\BaseController::isAdmin() && $p_Company != null && $p_Company['id'] != 1)
                            <button id="status" type="button" class="btn btn-round btn-danger btn-deactivate" onclick="onClickChangeCompanyStatus()">{{ $p_Company->isActive() ? 'Desativar' : 'Ativar' }}</button>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="company_type_id">Tipo de empresa</label>
                            {{ Form::select('company_type_id', $p_CompanyTypes, $p_Company['company_type_id'], array('class'=>'form-control')) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="cnpj">CNPJ</label>
                            @if(\App\Http\Controllers\BaseController::isAdmin())
                                {{ Form::text('cnpj', $p_Company['cnpj'], array('class'=>'form-control', 'id' => 'cnpj')) }}
                            @else
                                {{ Form::text('cnpj', $p_Company['cnpj'], array('class'=>'form-control', 'id' => 'cnpj', 'readonly')) }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="company_name">Razão Social</label>
                            {{ Form::text('company_name', $p_Company['company_name'], array('class'=>'form-control')) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            {{ Form::text('email', $p_Company['email'], array('class'=>'form-control')) }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <div style="text-align: center; margin-right: auto">
                                <label for="logo">Logo</label>
                                </br>
                                <img id="image" style="width: 100px; height: 100px; background:url({{$p_Company['logo_url']}}) no-repeat;background-size:cover;"/>
                                </br>
                                {{ Form::file('image_file', array('data-filename-placement'=>'inside', 'onchange' => 'onChangeImage(this)', 'accept' => 'image/*', 'class' => 'btn btn-primary')) }}
                                </br>
                                <span>Altura: 200px</span>
                                </br>
                                <span>Largura: 200px</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <div style="text-align: center; margin-right: auto">
                                <label for="outer">Fachada</label>
                                </br>
                                <img id="image_outer" style="width: 200px; height: 100px; background:url({{$p_Company['photo_url']}}) no-repeat;background-size:cover;"/>
                                <br>
                                {{ Form::file('image_outer_file', array('data-filename-placement'=>'inside', 'onchange' => 'onChangeOuterImage(this)', 'accept' => 'image/*', 'class' => 'btn btn-primary')) }}
                                </br>
                                <span>Altura: 200px</span>
                                </br>
                                <span>Largura: 400px</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="description">Descrição</label>
                            {{ Form::textarea('description', $p_Company['description'], array('class'=>'form-control', 'rows' => 10)) }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="lunch">Almoço</label>
                            <div class="row" style="clear: both">
                                <div class="col-md-1">
                                    <span style="line-height: 39px">De:</span>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group bootstrap-timepicker">
                                        {{ Form::text('lunch_start', $p_Company['lunch_start'], array('class'=>'form-control timepicker-24')) }}
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" style="margin-bottom: 15px" type="button"><i class="fa fa-clock-o"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <span style="line-height: 39px; text-align: right">até:</span>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group bootstrap-timepicker">
                                        {{ Form::text('lunch_end',  $p_Company['lunch_end'], array('class'=>'form-control timepicker-24')) }}
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" style="margin-bottom: 15px" type="button"><i class="fa fa-clock-o"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="lunch">Jantar</label>
                    <div class="row" style="clear: both">
                        <div class="col-md-1">
                            <span style="line-height: 39px">De:</span>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group bootstrap-timepicker">
                                {{ Form::text('dinner_start',  $p_Company['dinner_start'], array('class'=>'form-control timepicker-24')) }}
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" style="margin-bottom: 15px" type="button"><i class="fa fa-clock-o"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <span style="line-height: 39px; text-align: right">até:</span>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group bootstrap-timepicker">
                                {{ Form::text('dinner_end', $p_Company['dinner_end'], array('class'=>'form-control timepicker-24')) }}
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" style="margin-bottom: 15px" type="button"><i class="fa fa-clock-o"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($p_Company === null)
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="country">País</label>
                            <br>
                            {{ Form::input('hidden', 'select_country', $p_Company['country'], array('id' => 'user_country')) }}
                            {{ Form::select('country', [], null, ['id' => 'country', 'onchange' => 'selectCountry(this.value)', 'class' => 'country form-control']) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="cep">CEP</label>
                            <br>
                            {{ Form::text('cep', $p_Company['cep'], array('class'=>'cep cep-form form-control', 'id' => 'cep')) }}
                            <a type="button" class='btn btn-primary search-cep'>Pesquisar</a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="state">Estado</label>
                            <br>
                            {{ Form::input('hidden', 'select_state', $p_Company['state'], array('id' => 'user_state')) }}
                            {{ Form::select('state', [], null, array('id' => 'state', 'onchange' => 'selectState(this.value)', 'class' => 'state form-control')) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="city">Cidade</label>
                            <br>
                            {{ Form::input('hidden', 'select_city', $p_Company['city'], array('id' => 'user_city')) }}
                            {{ Form::select('city', [], null, array('id' => 'city', 'onchange' => 'selectCity(this.value)', 'class' => 'city form-control')) }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                                <label for="street">Logradouro</label>
                                {{ Form::text('street', $p_Company['street'], array('class'=>'form-control street')) }}
                            </div>
                        </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="number">Numero</label>
                            {{ Form::text('street_number', $p_Company['street_number'], array('class'=>'form-control street_number')) }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="complement">Complemento</label>
                            {{ Form::text('complement', $p_Company['complement'], array('class'=>'form-control complement')) }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="neighborhood">Bairro</label>
                            {{ Form::text('neighborhood', $p_Company['neighborhood'], array('class'=>'form-control neighborhood')) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="tel">Telefone</label>
                            {{ Form::text('tel', $p_Company['tel'], array('class'=>'form-control', 'id' => 'tel')) }}
                        </div>
                    </div>
                </div>
                {{ Form::input('hidden', 'latitude', '') }}
                {{ Form::input('hidden', 'longitude', '') }}
                <div class="row map-row">
                    <div class="col-xs-12 col-md-12">
                        <iframe width="100%" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/search?key=AIzaSyByS6W9I7X4NnvrIjqaZlMv1J8lVtMOhuw&q=brasil">
                        </iframe>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="new_password">Senha do estabelecimento</label>
                            {{ Form::password('password', array('class'=>'form-control')) }}
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirmação da senha do estabelecimento</label>
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
                @if($p_Company === null)
                    {{ Form::submit('Salvar', array('class'=>'btn btn-round btn-primary btn-primary pull-right'))}}
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
                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
                </div>
            </section>
        </div>
    </div>
    @if($p_Company !== null)
    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading">
                    Endereços
                </header>
                <div class="panel-body">
                    <div class='col-sm-12'>
                        <a class="btn btn-primary pull-right" href="{{URL("/company/address/add/") . (\App\Http\Controllers\BaseController::isAdmin() ? '/' . $p_Company->id : '') }}">Add endereço</a>
                    </div>
                    <div class="adv-table">
                        <table class="display table table-bordered table-striped" id="addresses-table" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Endereço</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($p_Company->addresses as $c_Address)
                                    <tr>
                                        <td>{{ $c_Address->name }}</td>
                                        <td>{{ $c_Address->street . ' ' . $c_Address->street_number . ', ' . $c_Address->neighborhood . ', ' . $c_Address->city . ' - ' . str_replace("BR-", "", $c_Address->state) }}</td>
                                        <td><i class="fa {{ $c_Address->is_active ?  'fa-check' : 'fa-times' }}" ></i></td>
                                        <td>
                                            @if($c_Address->is_active)
                                            <a href="{{ url('/company/address/' . $c_Address->id) }}" title="Editar" type="button" class="btn btn-primary dark">
                                                <i class="ico-pencil"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
    @endif
@stop
@section('pageScript')
<script src="{{url('js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{url('js/bootstrap-timepicker/js/bootstrap-timepicker.js')}}"></script>
@if($p_Company !== null)
<script src="{{url('js/datatables/media/js/jquery.dataTables.min.js')}}"></script>
<script src="{{url('js/datatables/media/js/dataTables.bootstrap.js')}}"></script>
<script src="{{url('js/datatables/extensions/TableTools/js/dataTables.tableTools.min.js')}}"></script>
@endif
<script type="text/javascript">

$('.timepicker-24').timepicker({
    autoclose: true,
    minuteStep: 15,
    showSeconds: false,
    showMeridian: false,
    defaultTime: false
});

@if($p_Company !== null)
    $('form').keypress(function (event) {
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
@else
    $('form input:not(#cep), form select').keypress(function (event) {
        var keyCode = (event.keyCode ? event.keyCode : event.which);
        if (keyCode == '13') {
            event.preventDefault();
            $('input[type="submit"]').click();
        }
    });
    $('#cep').keypress(function (event) {
        var keyCode = (event.keyCode ? event.keyCode : event.which);
        if (keyCode == '13') {
            event.preventDefault();
            $('.btn.search-cep').click();
        }
    });
@endif
    function onChangeImage(imageInput)
    {
        if(imageInput.files && imageInput.files[0])
        {
            var reader = new FileReader();
            reader.onload = function(e)
            {
                document.getElementById('image').style.background = 'url(' + e.target.result + ') no-repeat center';
            };
            reader.readAsDataURL(imageInput.files[0]);
        }
    }

    function onChangeOuterImage(imageInput)
    {
        if(imageInput.files && imageInput.files[0])
        {
            var reader = new FileReader();
            reader.onload = function(e)
            {
                document.getElementById('image_outer').style.background = 'url(' + e.target.result + ') no-repeat center';
            };
            reader.readAsDataURL(imageInput.files[0]);
        }
    }
@if($p_Company !== null)
    function onChangeNewPwd()
    {
        $('.newPassword, .newPasswordConfirmation').attr('disabled', !$('.newPassword, .newPasswordConfirmation').attr('disabled'));
    }
@endif
    jQuery(function($)
    {
        $('#cnpj').mask('99.999.999/9999-99');
        @if($p_Company === null)
        $('#cep').mask('99.999-999');
        $('#tel').mask('(99)9999-9999?9');
        @endif
    });

    function onSubmitForm()
    {
        $('#cnpj').mask('99999999999999?9');
        @if($p_Company === null)
        $('#cep').mask('99999999');
        $('#tel').mask('9999999999?9');
        @endif

		var v_Form = document.forms['mainForm'];
        if(v_Form['trade_name'].value === null || v_Form['trade_name'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            @if($p_Company === null)
			$('#cep').mask('99.999-999');
			@endif
			$('#tel').mask('(99)9999-9999?9');
			$('.modal button.close').click();
            alert('O campo de nome fantasia deve ser preenchido');
            return false;
        }
		else if(!validateCNPJ(v_Form['cnpj'].value))
		{
			$('#cnpj').mask('99.999.999/9999-99');
			@if($p_Company === null)
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            @endif
			$('.modal button.close').click();
            alert('CNPJ inválido!');
            return false;
		}
        else if(v_Form['email'].value === null || v_Form['email'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            @if($p_Company === null)
            $('#cep').mask('99.999-999');
			$('#tel').mask('(99)9999-9999?9');
            @endif
			$('.modal button.close').click();
            alert('O campo de e-mail deve ser preenchido.');
            return false;
        }
		else if(v_Form['password'].value === null || v_Form['password'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            @if($p_Company === null)
            $('#cep').mask('99.999-999');
			$('#tel').mask('(99)9999-9999?9');
            @endif
            alert('A senha é obrigatória!');
            return false;
        }
        @if($p_Company === null)
        else if(v_Form['image_file'].value === null || v_Form['image_file'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
			$('#tel').mask('(99)9999-9999?9');
			$('.modal button.close').click();
            alert('Uma imagem de logo deve ser selecionada.');
            return false;
        }
        else if(v_Form['image_outer_file'].value === null || v_Form['image_outer_file'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
			$('#tel').mask('(99)9999-9999?9');
			$('.modal button.close').click();
            alert('Uma imagem de fachada deve ser selecionada.');
            return false;
        }
        else if(v_Form['latitude'].value === '' || v_Form['longitude'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O endereço deve ser preenchido corretamente');
            return false;
        }
        else if(v_Form['cep'].value === null || v_Form['cep'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de CEP deve ser preenchido');
            return false;
        }
        else if(v_Form['select_city'].value === null || v_Form['select_city'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de cidade deve ser preenchido');
            return false;
        }
        else if(v_Form['select_state'].value === null || v_Form['select_state'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de estado deve ser preenchido');
            return false;
        }
        else if(v_Form['street'].value === null || v_Form['street'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de logradouro deve ser preenchido');
            return false;
        }
        else if(v_Form['street_number'].value === null || v_Form['street_number'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de número deve ser preenchido');
            return false;
        }
        else if(v_Form['neighborhood'].value === null || v_Form['neighborhood'].value === '')
        {
            $('#cnpj').mask('99.999.999/9999-99');
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de bairro deve ser preenchido');
            return false;
        }
        @endif
        else if(v_Form['lunch_start'].value === null || v_Form['lunch_start'].value === '' || v_Form['lunch_end'].value === null || v_Form['lunch_end'].value === '')
        {
            alert('Os campos de horário de almoço devem ser preenchidos.');
            return false;
        }
        else if(v_Form['dinner_start'].value === null || v_Form['dinner_start'].value === '' || v_Form['dinner_end'].value === null || v_Form['dinner_end'].value === '')
        {
            alert('Os campos de horário de jantar devem ser preenchidos.');
            return false;
        }
    }

	function validateCNPJ(p_Cnpj)
	{
		p_Cnpj = p_Cnpj.replace(/[^\d]+/g,'');
		if(p_Cnpj == '')
			return false;
			     
		if (p_Cnpj.length != 14)
			return false;
	 
		if (p_Cnpj == "00000000000000" || p_Cnpj == "11111111111111" || p_Cnpj == "22222222222222" || p_Cnpj == "33333333333333" || p_Cnpj == "44444444444444" ||
			p_Cnpj == "55555555555555" || p_Cnpj == "66666666666666" || p_Cnpj == "77777777777777" || p_Cnpj == "88888888888888" || p_Cnpj == "99999999999999")
			return false;
			 
		var v_Size = p_Cnpj.length - 2
		var v_Numbers = p_Cnpj.substring(0,v_Size);
		var v_Digits = p_Cnpj.substring(v_Size);
		var v_Sum = 0;
		var v_Pos = v_Size - 7;
		for (i = v_Size; i >= 1; i--)
		{
			v_Sum += v_Numbers.charAt(v_Size - i) * v_Pos--;
			if (v_Pos < 2)
				v_Pos = 9;
		}
		var v_Result = v_Sum % 11 < 2 ? 0 : 11 - v_Sum % 11;
		if (v_Result != v_Digits.charAt(0))
			return false;
			 
		v_Size = v_Size + 1;
		v_Numbers = p_Cnpj.substring(0,v_Size);
		v_Sum = 0;
		v_Pos = v_Size - 7;
		for (i = v_Size; i >= 1; i--)
		{
			v_Sum += v_Numbers.charAt(v_Size - i) * v_Pos--;
			if (v_Pos < 2)
				v_Pos = 9;
		}
		v_Result = v_Sum % 11 < 2 ? 0 : 11 - v_Sum % 11;
		if (v_Result != v_Digits.charAt(1))
			return false;
		return true;
	}

	@if(\App\Http\Controllers\BaseController::isAdmin() && $p_Company != null && $p_Company['id'] != 1)
	function onClickChangeCompanyStatus()
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
		v_Xmlhttp.open("GET", "{{URL("/company/deactivate/" . $p_Company->id)}}", true);
		v_Xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		v_Xmlhttp.send();
	}
	@endif

    @if($p_Company !== null)
	function focusOnModal()
	{
        if($('#pwd').is(':visible'))
            $('#pwd').focus();
	}
	window.setInterval(focusOnModal, 100);
    @endif
	$(document).ready(function () {
		$('a.file-input-wrapper:has(input[name="image_file"]) span').text('Logo');
		$('a.file-input-wrapper:has(input[name="image_outer_file"]) span').text('Fachada');

        @if($p_Company === null)
        $('#state, #city, input[name="select_city"], input[name="select_state"], input[name="street_number"], input[name="street"], input[name="neighborhood"]').change(function(){
            var v_AddressString = $('input[name="street"]').val() + '+' + $('input[name="street_number"]').val() + '+' + $('input[name="neighborhood"]').val() + '+' + $('input[name="select_city"]').val() + '+' + $('input[name="select_state"]').val();
            v_AddressString = v_AddressString.replace(/ /g, '+');
            $('.map-row iframe').attr('src', "https://www.google.com/maps/embed/v1/search?key=AIzaSyByS6W9I7X4NnvrIjqaZlMv1J8lVtMOhuw&q=" + v_AddressString);

            $.ajax({
                url : 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=' + v_AddressString,
                type: "GET",
                dataType: "html",
                success: function(p_Data, textStatus, jqXHR)
                {
                    var v_Location = JSON.parse(p_Data)['results'][0]['geometry']['location'];
                    $('input[name="longitude"]').val(v_Location['lng']);
                    $('input[name="latitude"]').val(v_Location['lat']);
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    $('input[name="longitude"], input[name="latitude"]').empty();
                }
            });
        });
        @else
		if($('input[name="changePassword"]').is(':checked'))
			$('.newPassword, .newPasswordConfirmation').attr('disabled', false);

		$('#addresses-table').DataTable(
        {
            "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "sDom": '<"dt-panelmenu clearfix"lfr>t<"dt-panelfooter clearfix"ip>',
            tableTools: {
                "aButtons": []
            },
            "bAutoWidth": true,
            "sScrollX": "100%",
            "bScrollCollapse": true,
            "oLanguage":
            {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate":
                {
                    "sNext": "",
                    "sPrevious": "",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria":
                {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            "aoColumns": [{"bSortable": true},
                        {"bSortable": true},
                        {"bSortable": false},
                        {"bSortable": false}]
        });
        var v_FilterInput = $('.adv-table input[aria-controls="addresses-table"]').appendTo('.adv-table #addresses-table_filter');
        v_FilterInput.attr('placeholder', 'Pesquisar');
        $('.adv-table #addresses-table_filter label').text('');
        v_FilterInput.appendTo('.adv-table #addresses-table_filter label');
        @endif
	});
</script>
<script src="{{url('js/jquery.maskedinput.js')}}"></script>
@if($p_Company === null)
@include('js.country_state_city')
@endif
@stop