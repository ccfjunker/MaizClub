@extends('base')
@section('mainContent')
    <div class="row">
        <div class='col-lg-12'>
            <section class="panel">
                <header class="panel-heading">
                        @yield('panel-header')
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
                    @yield('content')
                </div>
            </section>
        </div>
    </div>
@stop