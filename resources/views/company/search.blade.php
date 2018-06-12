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
    #dynamic-table
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
    .dataTable tr td.actions-column
    {
        min-width: 180px;
    }
    .dataTable tr td.trade-name-column
    {
        min-width: 140px;
    }
    .dataTable tr td.cnpj-column
    {
        max-width: 120px;
    }
    .dataTable tr td.offers-column
    {
        min-width: 70px;
    }
    @media (max-width: 1180px)
    {
        .dataTable tr td.actions-column
        {
            max-width: 100px;
        }
    }
    @media (max-width: 1080px)
    {
        .dataTable tr td.trade-name-column
        {
            max-width: 120px;
        }
        .dataTable tr td.cnpj-column
        {
            max-width: 90px;
        }
    }
</style>
@stop
@section('panel-header')
    Estabelecimentos
@stop
@section('content')
    <div class='col-sm-12'>
        <a class="btn btn-primary pull-right" href="{{url("/company/add/")}}">Add estabelecimento</a>
    </div>
    <div class="adv-table">
        <table class="display table table-bordered table-striped" id="dynamic-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Fantasia</th>
                    <th>CNPJ</th>
                    <th>Status</th>
                    <th>Ofertas</th>
                    {{--<th>Recompensas</th>--}}
                    <th>Ações</th>
                </tr>
                <tr>
                    <td></td>
                    <td class="trade-name-column">Nome Fantasia</td>
                    <td class="cnpj-column">CNPJ</td>
                    <td>Status</td>
                    <td class="offers-column"></td>
                    {{--<td class="offers-column"></td>--}}
                    <td class="actions-column"></td>
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
<script type="text/javascript">
$(document).ready(function ()
{
    var v_Timer;
    // Setup - add a text input to the header cells
    $('#dynamic-table thead td').each( function ()
    {
        var v_Index = $(this).index();
        if(v_Index == 1 || v_Index == 2)
            $(this).html( '<input type="text" placeholder="Buscar" class="form-control" style="font-weight:normal"/>' );
        else if(v_Index == 3)
            $(this).html('<select class="form-control" style="font-weight:normal; max-width: 90px"><option value=""></option><option value="1">Ativo</option><option value="0">Inativo</option></select>');
    });

    /* DataTables */
    var v_Table = $('#dynamic-table').DataTable(
    {
        serverSide: true,
        ajax:
        {
            url: "{{ url('/dt/companies')}}"
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
                    {"bSortable": false},
                    {"bSortable": false},
//                    {"bSortable": false},
                    {"bSortable": false}]
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
        $('.display.table.table-bordered.dataTable thead td:eq('+colIdx+') select').on( 'change', function ()
        {
            v_Table.column( colIdx ).search( this.value ).draw();
        });
    });
});

function validateForm()
{
    var v_Size = document.getElementById('size');
    var v_Valid = document.getElementById('valid_for');
    if(v_Size == null || v_Size == "" || v_Size.value <= 0)
    {
        alert('O tamanho do lote deve ser maior que zero.');
        return false;
    }
    if(v_Valid == null || v_Valid == "" || v_Valid.value <= 0)
    {
        alert('A duração do lote deve ser maior que zero.');
        return false;
    }
    return true;
}
</script>
@stop