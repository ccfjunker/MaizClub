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
                    <p>Preencha o formulário do link abaixo para redefinir sua senha</p>
                    {{ url('password/reset/'. $token) }}.
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
