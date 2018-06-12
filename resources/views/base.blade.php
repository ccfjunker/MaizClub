<!DOCTYPE HTML>
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
		<style type="text/css">
        .footer-gafit {
            margin-top: 25px;
            font-weight: 600;
            text-align: center;
            padding-top:0;
            padding-bottom:0;
            height: 48px;
            line-height: 48px;
        }
        .footer-gafit .copy {
            background: #FFF;
            border-radius: 4px;
        }
        </style>
        @yield('pageCSS')
        <link rel="icon" href="{{url('/images/favicon.png')}}" />
	</head>
	<body>
    <header class="header fixed-top clearfix">
        <!--logo start-->
        <div class="brand">
            <a href="{{url("/")}}" class="logo">
                <img class="img-logo" src="{{url("/images/logo.png")}}" alt="QUERO" style="height: 22px; margin-top: 24px;">
            </a>
        </div>
        <!--logo end-->
        <div class="top-nav clearfix">
            <ul class="nav pull-right top-menu">
                <!-- user login dropdown start-->
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="username">{{ \App\Http\Controllers\BaseController::isClient() ? Auth::guard('client')->user()->name : Auth::guard('company')->user()->trade_name}}</span>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu extended logout">
                        <li><a href="{{url('logout')}}">Sair</a></li>
                    </ul>
                </li>
                <!-- user login dropdown end -->
            </ul>
        </div>
    </header>
    <aside>
        <div id="sidebar" class="nav-collapse">
            <!-- sidebar menu start-->
            <div class="leftside-navigation">
                <ul class="sidebar-menu" id="nav-accordion">
                    <li>
                        <a href="{{url("/client/search")}}">
                            <i class="fa fa-user"></i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    @if(\App\Http\Controllers\BaseController::isAdmin())
                        <li>
                            <a href="{{url("/company/search")}}">
                                <i class="fa fa-building-o"></i>
                                <span>Estabelecimentos</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{url("/company/offers/")}}">
                                <i class="fa fa-usd"></i>
                                <span>Ofertas</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{url("/company/prizes/")}}">
                            <i class="fa fa-trophy"></i>
                            <span>Recompensas</span>
                        </a>
                    </li>
                    <li class="sub-menu dcjq-parent-li">
                        <a href="#" class="dcjq-parent">
                            <i class=" fa fa-edit"></i>
                            <span>Configurações</span>
                            <span class="dcjq-icon"></span></a>
                        </a>
                        <ul class="sub">
                            <li><a href="{{url("/company/edit")}}">Editar</a></li>
                            <li><a href="{{url("/company/auth/" . Auth::guard('company')->id())}}">Autenticadores</a></li>
                            <li><a href="{{url("/company/contact/" . Auth::guard('company')->id())}}">Contatos</a></li>
                            @if(\App\Http\Controllers\BaseController::isAdmin())
                                <li><a href="{{url("/companyType/search")}}">Categorias</a></li>

                                <li><a href="{{url("/help")}}">Ajuda</a></li>
                                <li><a href="{{url("/usage_terms")}}">Termos de uso</a></li>
                                <li><a href="{{url("/about")}}">Sobre</a></li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- sidebar menu end-->
        </div>
    </aside>
    <!--sidebar end-->
    <section id='main-content'>
        <div class="wrapper">
            @yield('mainContent')
        </div>
        <div class="wrapper footer-gafit">
            <div class="copy">&copy; 2015 - <a href="http://www.gafit.com.br">GAFIT - Soluções em Automação</a> - Todos os Direitos Reservados.</div>
        </div>
    </section>

    </body>
    <script src="{{url('js/jquery.js')}}"></script>
    <script src="{{url('js/bootstrap.min.js')}}"></script>
    <script src="{{url('js/jquery.dcjqaccordion.2.7.js')}}"></script>
    <script src="{{url('js/jquery.scrollTo.min.js')}}"></script>
    <script src="{{url('js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js')}}"></script>
    <script src="{{url('js/jquery.nicescroll.js')}}"></script>
    <script src="{{url('js/scripts.js')}}"></script>
    <script src="{{url('js/bootstrap.file-input.js')}}"></script>
    @yield('pageScript')
</html>
