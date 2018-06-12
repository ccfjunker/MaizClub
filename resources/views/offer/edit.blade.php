@extends('main')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{url('/css/datepicker.css')}}">
    <style type="text/css">
        img
        {
            margin-bottom: 5px;
        }

        .readonly
        {
            border: none;
            background-color: #fff !important;
        }
    </style>
@stop
@section('panel-header')
    @if($p_IsPrize)
        Recompensa
    @else
        Oferta
    @endif
@stop
@section('content')
    {{ Form::open(array('name' => 'mainForm', 'url'=>url('/offer'), 'onsubmit' => 'return validateForm()', 'files'=>true)) }}
    @if($p_Add == false)
        {{ Form::input('hidden', 'offer_id', $p_Offer->id) }}
    @endif
    {{ Form::input('hidden', 'company_id', $p_CompanyId) }}
    {{ Form::input('hidden', 'is_prize', $p_IsPrize) }}

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="title">Título</label>
                {{ Form::text('title', $p_Offer == null ? '' : $p_Offer->title, array('id' => 'title', 'class'=>'form-control' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? 'readonly' : ''))) }}
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            @if($p_Add == false && $p_Offer->isActive())
                <a id="status" href="{{url('/offer/deactivate/' . $p_Offer->id)}}" class="btn btn-round btn-danger btn-deactivate">Desativar</a>
            @elseif ($p_Offer != null)
                <button id="status" type="button" disabled="disabled" class="btn btn-round btn-danger btn-deactivate">Desativado</button>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4">
            <div class="form-group">
                <label for="old_price">De</label>
                {{ Form::text('old_price', $p_Offer == null ? '' : $p_Offer->old_price, array('id' => 'old_price', 'class'=>'form-control' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? 'readonly' : ''))) }}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                <label for="price">Por</label>
                {{ Form::text('price', $p_Offer == null ? '' : $p_Offer->price, array('id' => 'price', 'class'=>'form-control' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? 'readonly' : ''))) }}
            </div>
        </div>
        @if(\App\Http\Controllers\BaseController::isAdmin() && $p_CompanyId == 1)
        <div class="{{!$p_IsPrize ? 'col-xs-6' : 'col-xs-4'}}">
            <div class="form-group">
                <label for="points">Pontos</label>
                {{ Form::number('points', $p_Offer == null ? '' : $p_Offer->points, array('id' => 'points', 'class'=>'form-control' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? 'readonly' : ''), 'min' => '0')) }}
            </div>
        </div>
        @else
            {{ Form::input('hidden', 'points', 0) }}
        @endif
    </div>

    <div class="form-group">
        <div style="text-align: center; margin-right: auto">
            <label for="logo">Foto</label>
            </br>
        <img id="image" style="width: 100px; height: 100px; {{ $p_Offer == null ? '' : 'background:url(' . $p_Offer['photo_url'] . '.jpg)'}} no-repeat;background-size:cover;"/>
        </br>
        @if($p_Add != false || !$p_CantEdit)
            {{ Form::file('image_file', array('data-filename-placement'=>'inside', 'onchange' => 'onChangeImage(this)', 'accept' => 'image/*', 'class' => 'btn btn-primary')) }}
            </br>
        @endif
        <span>Altura: 320px</span>
        </br>
        <span>Largura: 320px</span>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <label for="description">Descrição</label>
                {{ Form::textarea('description', $p_Offer == null ? '' : $p_Offer->description, array('id' => 'description', 'class'=>'form-control' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? 'readonly' : ''), 'rows' => 10)) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <label>Validade da oferta</label>
            <div class="row" style="margin-left: 0">
                <div class="col-md-3 col-xs-6">
                    <div class="row">Segunda-feira</div>
                    <div class="row">
                        {{ Form::checkbox('day_1_lunch', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 2) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Almoço</span>
                    </div>
                    <div class="row">
                        {{ Form::checkbox('day_1_dinner', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 3) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Jantar</span>
                    </div>
                    </br>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="row">Terça-feira</div>
                    <div class="row">
                        {{ Form::checkbox('day_2_lunch', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 4) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Almoço</span>
                    </div>
                    <div class="row">
                        {{ Form::checkbox('day_2_dinner', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 5) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Jantar</span>
                    </div>
                    </br>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="row">Quarta-feira</div>
                    <div class="row">
                        {{ Form::checkbox('day_3_lunch', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 6) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Almoço</span>
                    </div>
                    <div class="row">
                        {{ Form::checkbox('day_3_dinner', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 7) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Jantar</span>
                    </div>
                    </br>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="row">Quinta-feira</div>
                    <div class="row">
                        {{ Form::checkbox('day_4_lunch', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 8) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Almoço</span>
                    </div>
                    <div class="row">
                        {{ Form::checkbox('day_4_dinner', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 9) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Jantar</span>
                    </div>
                    </br>
                </div>
            </div>
            <div class="row" style="margin-left: 0">
                <div class="col-md-3 col-xs-6">
                    <div class="row">Sexta-feira</div>
                    <div class="row">
                        {{ Form::checkbox('day_5_lunch', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 10) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Almoço</span>
                    </div>
                    <div class="row">
                        {{ Form::checkbox('day_5_dinner', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 11) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Jantar</span>
                    </div>
                    </br>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="row">Sábado</div>
                    <div class="row">
                        {{ Form::checkbox('day_6_lunch', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 12) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Almoço</span>
                    </div>
                    <div class="row">
                        {{ Form::checkbox('day_6_dinner', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 13) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Jantar</span>
                    </div>
                    </br>
                </div>
                <div class="col-md-3 col-xs-6">
                    <div class="row">Domingo</div>
                    <div class="row">
                        {{ Form::checkbox('day_0_lunch', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 0) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Almoço</span>
                    </div>
                    <div class="row">
                        {{ Form::checkbox('day_0_dinner', null, ($p_Offer['enabled_at'] == null || (($p_Offer['enabled_at'] >> 1) & 0x01) == 0) ? false : true, array($p_CantEdit ? 'disabled' : '')) }}
                        <span>Jantar</span>
                    </div>
                    </br>
                </div>
                <div class="col-md-3 col-xs-6"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <label for="rule">Regra</label>
                @if($p_Add == false)
                    {{ Form::textarea('rule', $p_Offer->rules()->orderBy('id', 'DESC')->first()['rule'], array('class'=>'form-control' . ($p_CanEditRule ? '' : ' readonly'), ($p_CanEditRule ? '' : ' readonly'), 'rows' => 10)) }}
                @else
                    {{ Form::textarea('rule', '', array('id' => 'rule', 'class'=>'form-control', 'rows' => 10)) }}
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label for="activation_date">Data de ativação</label>
                {{ Form::text('activation_date', $p_Offer == null ? '' : \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $p_Offer->activation_date)->format('d/m/Y'), array('id' => 'activation_date', 'size' => '16', 'class'=>'form-control form-control-inline input-medium default-date-picker' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? 'readonly' : ''))) }}
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label for="valid_until">Válido até</label>
                {{ Form::text('valid_until', $p_Offer == null ? '' : \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $p_Offer->valid_until)->format('d/m/Y'), array('id' => 'valid_until', 'size' => '16', 'class'=>'form-control form-control-inline input-medium default-date-picker' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? 'readonly' : ''))) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label for="amount_allowed">Quantidade disponível</label>
                {{ Form::number('amount_allowed', $p_Offer == null ? '' : $p_Offer->amount_allowed, array('id' => 'amount_allowed', 'class'=>'form-control' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? 'readonly' : ''), 'min' => '0')) }}
            </div>
        </div>
        <div class="col-xs-6">
            @if($p_Add == false)
                <div class="form-group">
                    <label for="amount_used">Quantidade utilizada</label>
                    {{ Form::number('amount_used', $p_Offer == null ? '' : $p_Offer->amount_used, array('id' => 'amount_used', 'class' => 'form-control' . ($p_CantEdit ? ' readonly' : ''), ($p_CantEdit ? ' readonly' : ''), 'min' => '0')) }}
                </div>
            @endif
        </div>
    </div>

    @if($p_Add != false || !$p_CantEdit || $p_CanEditRule)
        {{ Form::submit('Salvar', array('class'=>'btn btn-round btn-primary pull-right')) }}
    @endif
    <button id="back" type="button" style="margin-right: 5px;" class="btn btn-round btn-default" onclick="window.history.back()">Voltar</button>
    {{ Form::close() }}
<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
@stop
@section('pageScript')
<script type="text/javascript">

    jQuery(function($)
    {
        $('.default-date-picker:not(.readonly)').datepicker({format: 'dd/mm/yyyy'});
    });

	{{--@if($p_Offer != null && BaseController::isCompany() && ($p_Offer['activation_date'] < \Carbon\Carbon::now() ||--}}
        {{--($p_Offer['date_deactivated'] != null &&  $p_Offer['date_deactivated'] < \Carbon\Carbon::now())))--}}
        {{--window.onload = function()--}}
        {{--{--}}
            {{--document.getElementById('price').readOnly = true;--}}
            {{--document.getElementById('title').readOnly = true;--}}
            {{--document.getElementById('old_price').readOnly = true;--}}
            {{--document.getElementById('points').readOnly = true;--}}
            {{--document.getElementById('description').readOnly = true;--}}
            {{--var v_ImageInput = document.getElementById('image_input');--}}
            {{--if(v_ImageInput != null)--}}
                {{--v_ImageInput.disabled = true;--}}
            {{--document.getElementById('activation_date').readOnly = true;--}}
            {{--document.getElementById('valid_until').readOnly = true;--}}
            {{--document.getElementById('amount_allowed').readOnly = true;--}}
            {{--@if(!$p_Offer->isActive())--}}
                {{--document.getElementById('rule').disabled = true;--}}
            {{--@endif--}}
        {{--};--}}
    {{--@endif--}}

    function onChangeImage(p_ImageInput)
    {
        if(p_ImageInput.files && p_ImageInput.files[0])
        {
            var reader = new FileReader();
            reader.onload = function(e)
            {
                document.getElementById('image').style.background = 'url(' + e.target.result + ') no-repeat center';
            };
            reader.readAsDataURL(p_ImageInput.files[0]);
        }
    }

    function validateForm()
    {
        $('#price').maskMoney('destroy');
        $('#price').maskMoney({allowZero: true, prefix: '', decimal: '.', thousands: ''});
        $('#price').maskMoney('mask');
        $('#old_price').maskMoney('destroy');
        $('#old_price').maskMoney({allowZero: true, prefix: '', decimal: '.', thousands: ''});
        $('#old_price').maskMoney('mask');
        var v_Form = document.forms['mainForm'];
        @if(\App\Http\Controllers\BaseController::isAdmin())
            var v_CompanyId = v_Form['company_id'].value;
            if(v_CompanyId == null || v_CompanyId == "")
            {
                alert("Preencha a empresa.");
                return false;
            }
        @endif
        var v_Price = v_Form['price'].value;
        if(v_Price == null || v_Price == "")
        {
            alert("Prencha o preço.");
            return false;
        }
        @if(\App\Http\Controllers\BaseController::isAdmin() && $p_CompanyId == 1)
        var v_Points = v_Form['points'].value;
        if(v_Points == null || v_Points == "")
        {
            alert("Prencha a pontuação.");
            return false;
        }
        @endif
        var v_Description = v_Form['description'].value;
        if(v_Description == null || v_Description == "")
        {
            alert("Prencha a descrição.");
            return false;
        }
        var v_Rule = v_Form['rule'].value;
        if(v_Rule == null || v_Rule == "")
        {
            alert("Prencha a regra.");
            return false;
        }
        @if($p_Add != false)
        var v_Image = v_Form['image_file'].value;
        if(v_Image == null || v_Image == "")
        {
            alert("Selecione uma imagem.");
            return false;
        }
        @endif
        var v_ActivationDate = v_Form['activation_date'].value;
        if(v_ActivationDate == null || v_ActivationDate == "")
        {
            alert("Prencha a data de ativação.");
            return false;
        }
        var v_ValidUntil = v_Form['valid_until'].value;
        if(v_ValidUntil == null || v_ValidUntil == "")
        {
            alert("Prencha a data de validade.");
            return false;
        }
        var v_Amount = v_Form['amount_allowed'].value;
        if(v_Amount == null || v_Amount == "")
        {
            alert("Prencha a quantidade permitida.");
            return false;
        }
        var v_AmountUsed = v_Form['amount_used'];
        if(v_AmountUsed != null && (v_AmountUsed.value == null || v_AmountUsed.value == ""  || parseInt(v_AmountUsed.value) > parseInt(v_Amount)))
        {
            alert("A quantidade permitida não pode ser menor que a quantidade utilizada.");
            return false;
        }
        if ($('input[type="checkbox"][name*="day_"]:checked').length == 0)
        {
            alert("Ao menos uma opção de validade deve ser selecionada.");
            return false;
        }
    }

	$(function()
    {
        $('#price').maskMoney({allowZero: true, prefix: 'R$', decimal: ',', thousands: '.'});
        $('#price').maskMoney('mask');
        $('#old_price').maskMoney({allowZero: true, prefix: 'R$', decimal: ',', thousands: '.'});
        $('#old_price').maskMoney('mask');
		
		$('#price.readonly').maskMoney('destroy');
		$('#old_price.readonly').maskMoney('destroy');
    });
	
	$(document).ready(function () {
		$('a.file-input-wrapper:has(input[name="image_file"]) span').text('Foto');
	});
</script>
<script src="{{url('js/jquery.maskmoney.js')}}"></script>
<script src="{{url('js/bootstrap-datepicker.js')}}"></script>
@stop