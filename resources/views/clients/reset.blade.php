<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>QUERO</title>
        <link rel="stylesheet" type="text/css" href="{{url('/packages/bootstrap/css/bootstrap.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/font-awesome/css/font-awesome.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/css/style.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/css/style-responsive.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/css/bootstrap-reset.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/css/custom-bomdecopo.css')}}">
        <link rel="icon" href="{{url('/images/favicon.png')}}" />
    </head>

    <body>
        <section id="container" >
            <div class="row">
                <div class="col-sm-12">
                    @if(Session::has('message'))
                        <div class="alert alert-success alert-block fade in">
                            <p>{{ Session::get('message') }}</p>
                        </div>
                        <?php
                            Session::forget('message');
                        ?>
                    @endif
                    @if(Session::has('error_message'))
                        <div class="alert alert-danger alert-block fade in">
                            <p>{{ Session::get('error_message') }}</p>
                        </div>
                        <?php
                            Session::forget('error_message');
                        ?>
                    @endif
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger alert-block fade in">
                            <p>{{ $error }}</p>
                        </div>
                    @endforeach
                    {{ Form::open(array('url'=>URL('/password/reset'), 'class'=>'form-signin form-signin-max-width')) }}
                    	<h2 class="form-signin-heading">Redefina sua senha</h2>
                        <div class="login-wrap">
                            <div class="user-login-info">
                                {{ Form::hidden('token', $token) }}
                                {{ Form::text('email', null, array('class'=>'form-control', 'placeholder'=>'E-mail')) }}
                                {{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'Senha')) }}
                                {{ Form::password('password_confirmation', array('class'=>'form-control', 'placeholder'=>'Confirmação de Senha')) }}
                            </div>
                            {{ Form::submit('Redefinir Senha', array('class'=>'btn btn-lg btn-login btn-block'))}}
                    	</div>
                    {{ Form::close() }}
                </div>
            </div>
        </section>
        <script src="{{url('js/jquery.js')}}"></script>
        <script src="{{url('js/bootstrap.min.js')}}"></script>
        <script src="{{url('js/jquery.dcjqaccordion.2.7.js')}}"></script>
        <script src="{{url('js/jquery.scrollTo.min.js')}}"></script>
        <script src="{{url('js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js')}}"></script>
        <script src="{{url('js/jquery.nicescroll.js')}}"></script>
        <script src="{{url('js/scripts.js')}}"></script>
        <script src="{{url('js/bootstrap.file-input.js')}}"></script>
    </body>
</html>
