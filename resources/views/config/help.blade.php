@extends('main')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTablesTemplate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/bootstrap-timepicker/css/timepicker.css')}}">
    <style type="text/css">
        .list-image-div
        {
            width:100px;
            height:100px;
            margin:0 auto;
        }
        .list-image
        {
            height:100%;
            margin:0 auto;
        }
        .dt-panelmenu, .dt-panelfooter
        {
            background: none;
        }
        #contacts-table {
            width: 99.9999% !important;
        }
        #contacts-table td
        {
            min-width: 60px;
        }
        #contacts-table .btn.btn-success
        {
            margin:0;
        }
        .marginButton {
            margin-right: 30px;
        }
    </style>
@stop
@section('panel-header')
    Ajuda
@stop
@section('content')
    <div class='col-sm-12'>
        <a href="{{url('/help/edit')}}" style="margin-right: 5px;" class='btn btn-primary pull-right'>Add imagem</a>
    </div>
    <div class="adv-table">
        <table id="contacts-table" class="display table table-bordered table-striped" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Ordem</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($p_Items as $c_Index => $c_Item)
                <tr>
                    <td> {{ $c_Index }} </td>
                    <td>
                        <div class="list-image-div">
                            <img class="list-image" src="{{$c_Item->value}}"/>
                        </div>
                    </td>
                    <td>
                        <a href="{{url('/help/edit/' . $c_Item->id)}}" title='Editar' style="margin-right: 5px;"  class='btn btn-primary'><i class='ico-pencil'></i></a>
                        <a href="{{url('/help/delete/' . $c_Item->id)}}" title="Excluir" type="button" class="btn btn-primary dark"><i class="fa fa-trash-o"></i></a>
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
            $('#contacts-table').DataTable(
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
                            {"bSortable": false}]
                    });
            var v_FilterInput = $('input[aria-controls="contacts-table"]').appendTo('#contacts-table_filter');
            v_FilterInput.attr('placeholder', 'Pesquisar');
            $('#contacts-table_filter label').text('');
            v_FilterInput.appendTo('#contacts-table_filter label');
        });
    </script>
@stop