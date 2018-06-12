@extends('main')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTablesTemplate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/bootstrap-timepicker/css/timepicker.css')}}">
    <style type="text/css">
        .list-image-div
        {
            width: 80px;
            height:80px;
            margin-left: auto;
            margin-right: auto;
        }
        .list-image
        {
            width:100%;
            height:100%;
        }
        .dt-panelmenu, .dt-panelfooter
        {
            background: none;
        }
        #offers-table {
            width: 99.9999% !important;
        }
        #offers-table td
        {
            min-width: 60px;
        }
        #offers-table .btn.btn-success
        {
            margin:0;
        }
        .profile-desk
        {
            border-right: 0!important;
        }
    </style>
@stop
@section('panel-header')
    @if($p_IsPrize)
        Recompensas
    @else
        Ofertas
    @endif
@stop
@section('content')
    <div class="panel-body profile-information">
        <div class="col-md-12">
            <div class="profile-pic text-center">
                @if(!empty($p_Company->logo_url))
                <img src={{$p_Company->logo_url}}>
                @endif
            </div>
            <div class="profile-desk text-center">
                <h1>{{ $p_Company->trade_name }}</h1>
            </div>
        </div>
    </div>
    <div class='col-sm-12'>
        <a href="{{url('/offer/add/' . ($p_IsPrize ? 'prize/' : 'offer/') . $p_Company->id)}}" class="btn btn-primary pull-right">Add {{$p_IsPrize ? 'recompensa' : 'oferta'}}</a>
    </div>
    <div class="adv-table">
        <table id="offers-table" class="display table table-bordered table-striped" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Data de ativação</th>
                <th>Data de término</th>
                <th>Uso</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
                @foreach($p_Company->offers as $c_Offer)
                    @if($p_IsPrize == $c_Offer->is_prize)
                        <tr>
                            <td class="table-middle">{{ $c_Offer->id }}</td>
                            <td>
                                <div class="list-image-div">
                                    <img class="list-image" src="{{$c_Offer->photo_url}}s.jpg" onclick="onClickEditOffer({{$c_Offer->id}})"/>
                                </div>
                            </td>
                            <td class="name table-middle">{{ $c_Offer->title }}</td>
                            <td class="table-middle">{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $c_Offer->activation_date)->format('d/m/y H:i') }}</td>
                            <td class="table-middle">{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $c_Offer->valid_until)->format('d/m/y H:i') }}</td>
                            <td class="table-middle">{{ $c_Offer->amount_used }}/{{ $c_Offer->amount_allowed }}</td>
                            <td class="table-middle"><i class="fa {{ $c_Offer->isActive() ? 'fa-check' : 'fa-times' }}"></i>
                            </td>
                            <td class="table-middle">
                                <a href="{{url('/offer/statistics/')}}/{{ $c_Offer->id }}" title="Estatísticas" style="margin-right: 5px;" class="btn btn-primary"><i class="ico-stats"></i></a>
                                <a href="{{url('/offer/edit/')}}/{{ $c_Offer->id }}" title="Editar" style="margin-right: 5px;" class="btn btn-primary" ><i class="ico-pencil"></i></a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@stop
@section('pageScript')
    <script src="{{url('js/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{url('js/datatables/media/js/dataTables.bootstrap.js')}}"></script>
    <script src="{{url('js/datatables/extensions/TableTools/js/dataTables.tableTools.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#offers-table').DataTable(
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
                            {"bSortable": false},
                            {"bSortable": true},
                            {"bSortable": true},
                            {"bSortable": true},
                            {"bSortable": true},
                            {"bSortable": false},
                            {"bSortable": false}]
                    });
            var v_FilterInput = $('input[aria-controls="offers-table"]').appendTo('#offers-table_filter');
            v_FilterInput.attr('placeholder', 'Pesquisar');
            $('#offers-table_filter label').text('');
            v_FilterInput.appendTo('#offers-table_filter label');
        });
    </script>
@stop