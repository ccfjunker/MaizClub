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
                Termos de uso
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
                {{ Form::open(array('url'=>URL('/usage_terms'), 'id' => 'mainForm', 'onsubmit' => 'return onSubmitForm()')) }}

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
    function onSubmitForm()
    {
    }

    $(document).ready(function ()
    {
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