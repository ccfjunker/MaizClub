@extends('main')
@section('pageCSS')
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTablesTemplate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/js/datatables/media/css/dataTables.bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/css/daterangepicker.css')}}">
    <style type="text/css">
        .list-image-div
        {
            width:100px;
            height:100px;
        }
        .profile-desk {
            border-right: none !important;
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
        .marginButton {
            margin-right: 30px;
        }
        #daterangepicker2 {
            width: 100%;
        }
    </style>
@stop
@section('panel-header')
    Estatísticas
@stop
@section('content')
    <div class="panel-body profile-information">
       <div class="col-md-3">
           <div class="profile-pic text-center">
               <img src="{{$p_Offer['photo_url']}}">
               <input id="p_ID" type="hidden" value="{{ $p_Offer['id'] }}"/>
           </div>
       </div>
       <div class="col-md-9">
           <div class="profile-desk">
               <h1>{{ $p_Offer['is_prize'] == 1 ? "Recompensa" : "Oferta" }} ID: {{ $p_Offer['id'] }}</h1>
               <span class="text-muted"></span>
               <p style="font-size: 1.2em">
                   <br/>
                   {{ $p_Offer['title'] }}
                   <br/>
                   Preço: {{ ($p_Offer['is_prize'] == 0 ? 'de R$' . str_replace('.', ',', $p_Offer['old_price']) . ' por R$' : 'R$') . str_replace('.', ',', $p_Offer['price']) }}
                   <br/>
                   Pontos: {{ $p_Offer['points'] }}
               </p>
           </div>
       </div>
    </div>
    <div class="adv-table">
        <table id="dynamic-table" class="display table table-bordered table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Autenticado</th>
                    @if(\App\Http\Controllers\BaseController::isAdmin())
                        <th>Pontos QUERO</th>
                    @endif
                </tr>
                <tr>
                    <td>Cliente</td>
                    <td>Data</td>
                    <td></td>
                    @if(\App\Http\Controllers\BaseController::isAdmin())
                        <td></td>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($p_PointsLog as $c_PointsLog)
                    <tr>
                        <td> {{ $c_PointsLog['client']['name'] }} </td>
                        <td class="date"> {{ $c_PointsLog['date'] }} </td>
                        <td> {{ $c_PointsLog['checked_out'] == 1 ?  $c_PointsLog['company_auth_id'] : "não" }} </td>
                        @if(\App\Http\Controllers\BaseController::isAdmin())
                            <td> {{ $c_PointsLog['bonus_points'] }} </td>
                        @endif
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
                if (v_Index === 1) {
                    $(this).html( '<div class="date pull-right" id="daterangepicker' + (v_Index + 1) + '">' +
                    '<input class="form-control dateInput" type="text"#footer{ id="dateRange' + (v_Index + 1) + '" placeholder="Buscar">' +
                    '</div>' );
                }
                else if (v_Index === 0) {
                    $(this).html('<input type="text" placeholder="Buscar" class="form-control" style="font-weight:normal"/>');
                }
            });

            /* DataTables */
            var v_Table = $('#dynamic-table').DataTable(
                    {
                        serverSide: true,
                        ajax:
                        {
                            url: "{{ url('/dt/offerStatistics')}}",
                            data: { "p_OfferID" : $('#p_ID').val() }
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
                        "aoColumns": [{"bSortable": true},
                            {"bSortable": true},
                            {"bSortable": false}
                    @if(\App\Http\Controllers\BaseController::isAdmin())
                            ,{"bSortable": true}
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