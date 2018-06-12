@extends('main')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTablesTemplate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTables.bootstrap.css')}}">
    <style type="text/css">
        .dt-panelmenu, .dt-panelfooter
        {
            background: none;
        }
        #dynamic-table {
            width: 99.9999% !important;
        }
        #dynamic-table td
        {
            min-width: 60px;
        }
        #dynamic-table .btn.btn-success
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
    Clientes
@stop
@section('content')
    @if(\App\Http\Controllers\BaseController::isAdmin())
    <div class='col-sm-12'>
        <a class="btn btn-primary pull-right" href="{{URL("/client/edit/")}}">Add cliente</a>
    </div>
    @endif
    <div class="adv-table">
        <table class="display table table-bordered table-striped" id="dynamic-table">
            <thead><tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Email</th>
                @if(\App\Http\Controllers\BaseController::isAdmin())
                    <th>Status</th>
                @endif
                <th>Ações</th>
            </tr>
            <tr>
                <td></td>
                <td>Nome</td>
                <td>CPF</td>
                <td>Email</td>
                @if(\App\Http\Controllers\BaseController::isAdmin())
                    <td>Status</td>
                @endif
                <td></td>
            </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
@stop
@section('pageScript')
<script src="{{url('js/datatables/media/js/jquery.dataTables.min.js')}}"></script>
<script src="{{url('js/datatables/media/js/dataTables.bootstrap.js')}}"></script>
<script src="{{url('js/datatables/extensions/TableTools/js/dataTables.tableTools.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function () {
        var v_Timer;
        // Setup - add a text input to the header cells
        $('#dynamic-table thead td').each( function ()
        {
            var v_Index = $(this).index();
            if(v_Index > 0)
            {
                if(v_Index  < 4)
                    $(this).html( '<input type="text" placeholder="Buscar" class="form-control" style="font-weight:normal"/>' );
                @if(\App\Http\Controllers\BaseController::isAdmin())
                else if (v_Index == 4)
                    $(this).html('<select class="form-control" style="font-weight:normal; max-width: 80px"><option value=""></option><option value="1">Ativo</option><option value="0">Inativo</option></select>');
                @endif
            }
        });

        /* DataTables */
        var v_Table = $('#dynamic-table').DataTable(
        {
            serverSide: true,
            ajax:
            {
                url: "{{ URL('/dt/clients')}}"
            },
            "orderCellsTop": true,
            "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "sDom": "<'dt-panelmenu clearfix'lTr>t<'dt-panelfooter clearfix'ip>",
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
            "order": [[ 0, "desc" ]],
            "aoColumns": [{"bSortable": true},
                {"bSortable": true},
                {"bSortable": true},
                {"bSortable": true},
            @if(\App\Http\Controllers\BaseController::isAdmin())
                {"bSortable": false},
            @endif
                {"bSortable": false}],
            "columnDefs": [
            @if(\App\Http\Controllers\BaseController::isAdmin())
                { "width": "12%", "targets": 5 }
            @else
                { "width": "12%", "targets": 4 }
            @endif
            ]
        });

        v_Table.columns().eq( 0 ).each( function ( colIdx )
        {
            $('.display.table.table-bordered.dataTable thead td:eq('+colIdx+') input').on( 'keyup', function (e)
            {
                var code = e.keyCode || e.which;
                if (code != '9')
                {
                    var v_Value = this.value;
                    clearInterval(v_Timer);  //clear any interval on key up
                    v_Timer = setTimeout(function() { //then give it a second to see if the user is finished
                        v_Table.column( colIdx ).search( v_Value ).draw();
                    }, 1000);
                }
            }).on( 'keydown', function (e)
            {
                var code = e.keyCode || e.which;
                if (code == '9')
                {
                    var v_Value = this.value;
                    clearInterval(v_Timer);  //clear any interval on key up
                    v_Timer = setTimeout(function() { //then give it a second to see if the user is finished
                        v_Table.column( colIdx ).search( v_Value ).draw();
                    }, 1000);
                }
            });
            @if(\App\Http\Controllers\BaseController::isAdmin())
            $('.display.table.table-bordered.dataTable thead td:eq('+colIdx+') select').on( 'change', function ()
            {
                v_Table.column( colIdx ).search( this.value ).draw();
            });
            @endif
        });

        $('.panel-body.pn select[name="responsive_table_length"]').addClass('form-control input-sm');
	});
</script>
@stop