@extends('main')
@section('pageCSS')
    <style type="text/css">
        img
        {
            margin-bottom: 5px;
        }
    </style>
@stop
@section('panel-header')
    {{ $p_Type == null ? "Nova categoria" : $p_Type['name']}}
@stop
@section('content')
    <div class="position-center">
        {{ Form::open(array('id' => 'mainForm', 'url'=>url('/companyType/edit'), 'onsubmit' => 'return validateForm()', 'files' => true)) }}
        @if($p_Type != null)
            {{ Form::input('hidden', 'id', $p_Type['id']) }}
        @endif

        <div class="form-group">
            <div style="text-align: center; margin-right: auto">
                <label for="logo">Imagem</label>
                </br>
                <img id="image" style="width: 100px; height: 100px; {{ $p_Type == null ? '' : 'background:url(' . $p_Type['photo_url'] . ')'}} no-repeat;background-size:cover;"/>
                </br>
                {{ Form::file('image_file', array('data-filename-placement'=>'inside', 'onchange' => 'onChangeImage(this)', 'accept' => 'image/*', 'class' => 'btn btn-primary')) }}
                </br>
                <span>Altura: 50px</span>
                </br>
                <span>Largura: 50px</span>
            </div>
        </div>

        <div class="form-group">
            <label for="name">Nome</label>
            {{ Form::text('name', $p_Type['name'], array('class'=>'form-control')) }}
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
	        alert("Preencha o nome da categoria.");
	        return false;
	    }
        @if($p_Type == null)
        var v_Image = v_Form['image_file'].value;
        if(v_Image == null || v_Image == "")
        {
            alert("Selecione uma imagem.");
            return false;
        }
        @endif
	}


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

    $(document).ready(function () {
        $('a.file-input-wrapper:has(input[name="image_file"]) span').text('Imagem');
    });
</script>
@stop