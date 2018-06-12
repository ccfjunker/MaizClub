@extends('main')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTablesTemplate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/css/daterangepicker.css')}}">
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
        .profile-desk
        {
            border-right: 0!important;
        }
    </style>
@stop
@section('panel-header')
    Estatísticas
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
                <input id="p_ID" type="hidden" value="{{ $p_Company['id'] }}"/>
            </div>
        </div>
    </div>
    <div class="adv-table">
        <table class="display table table-bordered table-striped" id="dynamic-table">
            <thead>
            <tr>
                <th>Cliente</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>ID Oferta</th>
                <th>Valor Gasto</th>
                <th>Pontos</th>
                @if(\App\Http\Controllers\BaseController::isAdmin())
                    <th>Pontos QUERO</th>
                @endif
                <th>Autenticado</th>
            </tr>
            <tr>
                <td>Estabelecimento</td>
                <td>Data</td>
                <td>Tipo</td>
                <td>ID Oferta</td>
                <td></td>
                <td></td>
                @if(\App\Http\Controllers\BaseController::isAdmin())
                    <td></td>
                @endif
                <td>Autenticado</td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@stop
@section('pageScript')
<script src="{{url('js/datatables/media/js/jquery.dataTables.min.js')}}"></script>
<script src="{{url('js/datatables/media/js/dataTables.bootstrap.js')}}"></script>
<script src="{{url('js/datatables/extensions/TableTools/js/dataTables.tableTools.min.js')}}"></script>
<script src="{{url('js/datatables/extensions/TableTools/js/moment.min.js')}}"></script>
<script src="{{url('js/datatables/extensions/TableTools/js/datetime-moment.js')}}"></script>
<script src="{{url('js/daterange/daterangepicker.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var v_Timer;
        // Setup - add a text input to the header cells
        $('#dynamic-table thead td').each( function ()
        {
            var v_Index = $(this).index();
            if(v_Index >= 0) {
                if (v_Index === 1) {
                    $(this).html( '<div class="date pull-right" id="daterangepicker' + (v_Index + 1) + '">' +
                    '<input id="dateRange2" class="form-control dateInput" type="text"#footer{ id="dateRange' + (v_Index + 1) + '" placeholder="Buscar">' +
                    '</div>' );
                }
                else if (v_Index === 2) {
                    $(this).html(
                            '<select id="select2" class="form-control" style="font-weight:normal;">' +
                            '<option value=""></option>' +
//                            '<option value="check-in">Check-in</option>' +
//                            '<option value="check-out">Check-out</option>' +
                            '<option value="oferta">Oferta</option>' +
                            '<option value="recompensa">Recompensa</option>' +
                            '</select>');
                }
                else if (v_Index === 0 || v_Index === 3) {
                    $(this).html('<input type="text" placeholder="Buscar" class="form-control" style="font-weight:normal"/>');
                }
            }
        });

        /* DataTables */
        var v_Table = $('#dynamic-table').DataTable(
                {
                    serverSide: true,
                    ajax:
                    {
                        url: "{{ url('/dt/pointsLog')}}",
                        data: { "p_CompanyID" : $('#p_ID').val(), "p_ClientID" : null }
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
                    "order": [[ 1, "desc" ]],
                    "aoColumns": [{"bSortable": false},
                        {"bSortable": true},
                        {"bSortable": false},
                        {"bSortable": true},
                        {"bSortable": false},
                        {"bSortable": false},
                        @if(\App\Http\Controllers\BaseController::isAdmin())
                        {"bSortable": false},
                        @endif
                        {"bSortable": false}],
                    "columnDefs": [
                        { "width": "20%", "targets": 1 },
                        { "width": "13%", "targets": 2 }
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
            $('.display.table.table-bordered.dataTable thead td:eq('+colIdx+') #dateRange2').on( 'blur', function ()
            {
                v_Table.column( colIdx ).search( this.value ).draw();
            });
            $('.display.table.table-bordered.dataTable thead td:eq('+colIdx+') #select2').on( 'change', function ()
            {
                v_Table.column( colIdx ).search( this.value ).draw();
            });
        });

        // daterange plugin options
        var rangeOptions = {
            ranges: {
                'Hoje': [moment(), moment()],
                'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            opens: 'right'
        };
        //Init daterange plugin
        $('#daterangepicker2').daterangepicker(
                rangeOptions
        );
        $('#daterangepicker2').on('apply.daterangepicker', function(ev, picker)
        {
            $('#dateRange2').blur();
        });
        $('.panel-body.pn select[name="responsive_table_length"]').addClass('form-control input-sm');
    });
</script>
@stop