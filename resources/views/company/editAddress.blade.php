@extends('main')
@section('pageCSS')
<link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTablesTemplate.css')}}">
<link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTables.bootstrap.css')}}">
    <style type="text/css">
        a:hover
        {
            color: #000099;
            text-decoration: underline;
        }
        .map-row
        {
            margin-bottom: 15px;
        }
    </style>
@stop
@section('panel-header')
    @if($p_CompanyId === null)
        Editar endereço
    @else
        Novo endereço
    @endif
@stop
@section('content')
    {{ Form::open(array('url'=>url('company/address'), 'id' => 'mainForm', 'onsubmit' => 'return onSubmitForm()', 'files'=>true)) }}
    @if($p_CompanyId === null)
        {{ Form::input('hidden', 'id', $p_Address->id) }}
        {{ Form::input('hidden', 'company_id', $p_Address->company_id) }}
    @else
        {{ Form::input('hidden', 'company_id', $p_CompanyId) }}
    @endif
    {{ Form::input('hidden', 'latitude', $p_Address == null ? '' : $p_Address->latitude) }}
    {{ Form::input('hidden', 'longitude', $p_Address == null ? '' : $p_Address->longitude) }}
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="complement">Nome</label>
                {{ Form::text('name', $p_Address == null ? '' : $p_Address->name, array('class'=>'form-control name')) }}
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            @if($p_Address != null)
                <a id="status" type="button" class="btn btn-round btn-danger btn-deactivate" href="{{url("/company/address/deactivate/" . $p_Address->id)}}">Desativar</a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="country">País</label>
                <br>
                {{ Form::input('hidden', 'select_country', $p_Address == null ? '' : $p_Address->country, array('id' => 'user_country')) }}
                {{ Form::select('country', [], null, ['id' => 'country', 'onchange' => 'selectCountry(this.value)', 'class' => 'country form-control']) }}
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="cep">CEP</label>
                <br>
                {{ Form::text('cep', $p_Address == null ? '' : $p_Address->cep, array('class'=>'cep cep-form form-control', 'id' => 'cep')) }}
                <a type="button" class='btn btn-primary search-cep'>Pesquisar</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="state">Estado</label>
                <br>
                {{ Form::input('hidden', 'select_state', $p_Address == null ? '' : $p_Address->state, array('id' => 'user_state')) }}
                {{ Form::select('state', [], null, array('id' => 'state', 'onchange' => 'selectState(this.value)', 'class' => 'state form-control')) }}
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="city">Cidade</label>
                <br>
                {{ Form::input('hidden', 'select_city', $p_Address == null ? '' : $p_Address->city, array('id' => 'user_city')) }}
                {{ Form::select('city', [], null, array('id' => 'city', 'onchange' => 'selectCity(this.value)', 'class' => 'city form-control')) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="street">Logradouro</label>
                {{ Form::text('street', $p_Address == null ? '' : $p_Address->street, array('class'=>'form-control street')) }}
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="number">Número</label>
                {{ Form::text('street_number', $p_Address == null ? '' : $p_Address->street_number, array('class'=>'form-control street_number')) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="complement">Complemento</label>
                {{ Form::text('complement', $p_Address == null ? '' : $p_Address->complement, array('class'=>'form-control complement')) }}
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="neighborhood">Bairro</label>
                {{ Form::text('neighborhood', $p_Address == null ? '' : $p_Address->neighborhood, array('class'=>'form-control neighborhood')) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="tel">Telefone</label>
                {{ Form::text('tel', $p_Address == null ? '' : $p_Address->tel, array('class'=>'form-control', 'id' => 'tel')) }}
            </div>
        </div>
    </div>

    <div class="row map-row">
        <div class="col-xs-12 col-md-12">
            @if ($p_Address != null)
            <iframe width="100%" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/search?key=AIzaSyByS6W9I7X4NnvrIjqaZlMv1J8lVtMOhuw&q= {{ str_replace(" ", "+", $p_Address->street) . '+' . $p_Address->street_number . '+' . $p_Address->neighborhood . '+' . str_replace(" ", "+", $p_Address->city) }}">
            </iframe>
            @else
            <iframe width="100%" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/search?key=AIzaSyByS6W9I7X4NnvrIjqaZlMv1J8lVtMOhuw&q=brasil">
            </iframe>
            @endif
        </div>
    </div>
    {{ Form::submit('Salvar', array('class'=>'btn btn-round btn-primary btn-primary pull-right'))}}

    <button id="back" type="button" style="margin-right: 5px;" class="btn btn-round btn-default" onclick="window.history.back()">Voltar</button>
    {{ Form::close() }}
    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
@stop
@section('pageScript')
<script type="text/javascript">
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
    jQuery(function($)
    {
        $('#cep').mask('99.999-999');
        $('#tel').mask('(99)9999-9999?9');
    });

    function onSubmitForm()
    {
        $('#cep').mask('99999999');
        $('#tel').mask('9999999999?9');
        var v_Form = document.forms['mainForm'];
        if(v_Form['latitude'].value === '' || v_Form['longitude'].value === '')
        {
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O endereço deve ser preenchido corretamente');
            return false;
        }
        else if(v_Form['cep'].value === null || v_Form['cep'].value === '')
        {
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de CEP deve ser preenchido');
            return false;
        }
        else if(v_Form['select_city'].value === null || v_Form['select_city'].value === '')
        {
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de cidade deve ser preenchido');
            return false;
        }
        else if(v_Form['select_state'].value === null || v_Form['select_state'].value === '')
        {
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de estado deve ser preenchido');
            return false;
        }
        else if(v_Form['street'].value === null || v_Form['street'].value === '')
        {
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de logradouro deve ser preenchido');
            return false;
        }
        else if(v_Form['street_number'].value === null || v_Form['street_number'].value === '')
        {
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de número deve ser preenchido');
            return false;
        }
        else if(v_Form['neighborhood'].value === null || v_Form['neighborhood'].value === '')
        {
            $('#cep').mask('99.999-999');
            $('#tel').mask('(99)9999-9999?9');
            alert('O campo de bairro deve ser preenchido');
            return false;
        }
    }

	$(document).ready(function () {
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
	});
</script>
<script src="{{url('js/jquery.maskedinput.js')}}"></script>
@include('js.country_state_city')
@stop