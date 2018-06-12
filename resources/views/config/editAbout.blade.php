@extends('base')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{url('/js/summernote/summernote.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/summernote/summernote-bs3.css')}}">
    <style type="text/css">
        .editor-container
        {
            width:322px;
            margin: 0 auto 20px;
        }
    </style>
@stop
@section('mainContent')
<div class="row">
    <div class='col-sm-12'>
        <section class="panel">
            <header class="panel-heading">
                Sobre
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
                {{ Form::open(array('url'=>url('/about'), 'id' => 'mainForm', 'onsubmit' => 'return onSubmitForm()', 'files'=>true)) }}

                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <div class="form-group">
                                <div style="text-align: center; margin-right: auto">
                                    <label for="logo">Imagem</label>
                                    </br>
                                    <img id="image" style="width: 322px; height: 160px; background:url({{$p_Photo['value']}}) no-repeat;background-size:cover;"/>
                                    </br>
                                    {{ Form::file('image_file', array('data-filename-placement'=>'inside', 'onchange' => 'onChangeImage(this)', 'accept' => 'image/*', 'class' => 'btn btn-primary')) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="value"></label>
                                <div class="editor-container">
                                    {{ Form::textarea('value', $p_Item != null ? $p_Item['value'] : '', array('id' => 'value', 'class'=>'form-control summernote', 'rows' => 10)) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <button id="back" type="button" style="margin-right: 5px;" class="btn btn-round btn-default" onclick="window.history.back()">Voltar</button>

                    {{ Form::submit('Salvar', array('class'=>'btn btn-round btn-primary btn-primary pull-right'))}}
                {{ Form::close() }}
                <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
                </div>
            </section>
        </div>
    </div>
@stop
@section('pageScript')
<script type="text/javascript" src="{{url('/js/summernote/summernote.js')}}"></script>
<script type="text/javascript" src="{{url('/js/summernote/summernote-pt-BR.js')}}"></script>
<script type="text/javascript">
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

    function onSubmitForm()
    {
        @if($p_Item === null)
        if(v_Form['image_file'].value === null || v_Form['image_file'].value === '')
        {
            alert('Uma imagem de deve ser selecionada.');
            return false;
        }
        @endif
    }

    $(document).ready(function ()
    {
        $('a.file-input-wrapper:has(input[name="image_file"]) span').text('Imagem');
        // Init Summernote
        $('.summernote').summernote({
            lang: 'pt-BR',
            height: 300, //set editable area's height
            focus: true, //set focus editable area after Initialize summernote
            oninit: function() {},
            onChange: function(contents, $editable) {},
            onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0], editor, welEditable);
            }
        });
    });
</script>
@stop