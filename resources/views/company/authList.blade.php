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
        .dt-panelmenu, .dt-panelfooter
        {
            background: none;
        }
        #auths-table
        {
            width: 99.9999% !important;
        }
        .table.dataTable th
        {
            min-width: 30px;
        }
        .table.dataTable .btn.btn-success
        {
            margin:0;
        }
        .dataTable tbody tr td
        {
            line-height: 34px;
        }
    </style>
@stop
@section('panel-header')
    Lista de Autenticadores
@stop
@section('content')
    <div class='col-sm-12'>
        <a class="btn btn-primary pull-right" href="{{url('/company/' . $p_CompanyId . '/editAuth')}}">Add autenticador</a>
    </div>
    <div class="adv-table">
        <table class="display table table-bordered table-striped" id="auths-table" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Senha</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($p_CompanyAuth as $c_Auth)
                    <tr>
                        <td>{{ $c_Auth->name }}</td>
                        <td>{{ $c_Auth->password }}</td>
                        <td>{{ $c_Auth->address->name }}</td>
                        <td><i class="fa {{ $c_Auth->is_active ?  'fa-check' : 'fa-times' }}" ></i></td>
                        <td>
                            @if($c_Auth->is_active)
                            <a href="{{url('/company/' . $p_CompanyId . '/editAuth/' . $c_Auth->id)}}" title="Editar" type="button" class="btn btn-primary dark">
                                <i class="ico-pencil"></i>
                            </a>
                            <a href="{{url('/company/' . $p_CompanyId . '/deactivateAuth/' . $c_Auth->id)}}" title="Desativar" type="button" class="btn btn-primary dark">
                                <i class="fa fa-trash-o"></i>
                            </a>
                            @else
                            <a href="{{url('/company/' . $p_CompanyId . '/deactivateAuth/' . $c_Auth->id)}}" title="Ativar" type="button" class="btn btn-primary dark">
                                <i class="fa fa-check"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
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
        $('#auths-table').DataTable(
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
                        {"bSortable": true},
                        {"bSortable": true},
                        {"bSortable": false},
                        {"bSortable": false}]
        });
        var v_FilterInput = $('.adv-table input[aria-controls="auths-table"]').appendTo('.adv-table #auths-table_filter');
        v_FilterInput.attr('placeholder', 'Pesquisar');
        $('.adv-table #auths-table_filter label').text('');
        v_FilterInput.appendTo('.adv-table #auths-table_filter label');
    });

</script>
@stop