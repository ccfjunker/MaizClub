var EditableTable = function () {

    return {

        //main function to initiate the module
        init: function () {
            function restoreRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);

                for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                    oTable.fnUpdate(aData[i], nRow, i, false);
                }

                oTable.fnDraw();
            }

            function editRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);
                var jqTdsLength = jqTds.length;
                for(var index = 0; index < jqTdsLength - 2; index ++) {
                   jqTds[index].innerHTML = '<input type="text" class="form-control small" value="' + aData[index] + '">';
                }
                jqTds[jqTdsLength - 2].innerHTML = '<a class="edit orange-color" href="">Confirmar</a>';
                jqTds[jqTdsLength - 1].innerHTML = '<a class="cancel orange-color" href="">Cancelar</a>';
            }

            function saveRow(oTable, nRow) {
                var jqInputs = $('input:visible', nRow);
                var jqInputsLength = jqInputs.length;
                for(var index = 0; index < jqInputsLength; index ++) {
                    oTable.fnUpdate(jqInputs[index].value, nRow, index, false);
                }
                oTable.fnUpdate('<a class="edit orange-color" href="">Editar</a>', nRow, jqInputsLength, false);
                oTable.fnUpdate('<a class="delete orange-color" href="">Deletar</a>', nRow, jqInputsLength + 1, false);
                oTable.fnDraw();
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $('input:visible', nRow);
                var jqInputsLength = jqInputs.length;
                for(var index = 0; index < jqInputsLength; index ++) {
                    var $input = $(jqInputs[index]);
                    if($input.attr('type') == 'password') {
                        oTable.fnUpdate($input, nRow, index, false);
                    }
                    else {
                        oTable.fnUpdate(jqInputs[index].value, nRow, index, false);
                    }
                }
                oTable.fnUpdate('<a class="edit orange-color" href="">Editar</a>', nRow, jqInputsLength, false);
                oTable.fnUpdate('<a class="delete orange-color" href="">Deletar</a>', nRow, jqInputsLength + 1, false);
                oTable.fnDraw();
            }

            var oTable = $('#editable-sample').dataTable({
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "Todos"] // change per page values here
                ],
                // set the initial value
                "iDisplayLength": 5,
                "sDom": "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-6'i><'col-lg-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ registros por página",
                    "oPaginate": {
                        "sPrevious": "Anterior",
                        "sNext": "Próximo"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': false,
                        'aTargets': [0]
                    }
                ]
            });

            jQuery('#editable-sample_wrapper .dataTables_filter input').addClass("form-control medium"); // modify table search input
            jQuery('#editable-sample_wrapper .dataTables_length select').addClass("form-control xsmall"); // modify table per page dropdown

            var nEditing = null;

            $(document).on('click', '#editable-sample_new', function(e){
                e.preventDefault();
                var dataLength = $('th').length - 2;
                var array = [];
                for(var index = 0; index < dataLength; index++)
                    array.push('');
                array.push('<a class="cancel orange-color" data-mode="new" href="">Cancelar</a>');

                var aiNew = oTable.fnAddData(array);
                var nRow = oTable.fnGetNodes(aiNew[0]);
                editRow(oTable, nRow);
                nEditing = nRow;
            });

            $(document).on('click', '#editable-sample a.delete', function(e){
                e.preventDefault();

                var nRow = $(this).parents('tr')[0];
                oTable.fnDeleteRow(nRow);
            });

            $(document).on('click', '#editable-sample a.cancel', function(e){
                e.preventDefault();
                if ($(this).attr("data-mode") == "new") {
                    var nRow = $(this).parents('tr')[0];
                    oTable.fnDeleteRow(nRow);
                } else {
                    restoreRow(oTable, nEditing);
                    nEditing = null;
                }
            });

            $(document).on('click', '#editable-sample a.edit', function(e){
                e.preventDefault();

                /* Get the row as a parent of the link that was clicked on */
                var nRow = $(this).parents('tr')[0];

                if (nEditing !== null && nEditing != nRow && this.innerHTML != "Confirmar") {
                    /* Currently editing - but not this row - restore the old before continuing to edit mode */
                    restoreRow(oTable, nEditing);
                    editRow(oTable, nRow);
                    nEditing = nRow;
                } else if (nEditing == nRow && this.innerHTML == "Confirmar") {
                    /* Editing this row and want to save it */
                    saveRow(oTable, nEditing);
                    nEditing = null;
                } else {
                    /* No edit in progress - let's start one */
                    editRow(oTable, nRow);
                    nEditing = nRow;
                }
            });
        }

    };

}();