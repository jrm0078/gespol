"use strict";

$(".preloader2").fadeIn();
Cargar();
$(".preloader2").fadeOut();

function Cargar() { CargaTabla(); }

function CargaTabla() {
    $('#tbl_log').DataTable().destroy();

    if ($('#tbl_log thead tr').length < 2) {
        $('#tbl_log thead tr').clone(true).appendTo('#tbl_log thead');
    }
    $('#tbl_log thead tr:eq(1) th').each(function(i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="Buscar ' + title + '" />');
        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) table.column(i).search(this.value).draw();
        });
    });

    var table = $('#tbl_log').DataTable({
        "sDom": '<f>t<pl>',
        "bPaginate": true, "bLengthChange": true, "bFilter": true, "bInfo": false, "bAutoWidth": false,
        "pageLength": 50, "orderCellsTop": true, "fixedHeader": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "language": {
            "emptytable": "Sin registros de log", "infoEmpty": "Sin resultados",
            "lengthMenu": "Mostrar _MENU_ registros", "search": "Buscar:",
            "zeroRecords": "Sin resultados",
            "paginate": { "previous": "Anterior", "sNext": "Siguiente" }
        },
        "columnDefs": [{ "targets": 0, "visible": false, "searchable": false }],
        "processing": true, "serverSide": true, "order": [[0, "desc"]],
        "buttons": [
            { extend: 'excel', text: 'Excel' }, { extend: 'csv', text: 'CSV' },
            { extend: 'pdf', text: 'PDF' }, { extend: 'print', text: 'Imprimir' }, { extend: 'copy', text: 'Copiar' }
        ],
        "ajax": { type: "POST", data: {}, url: "inc/func_ajax.php/CargatablaLog", dataType: 'JSON' }
    });

    $(document).on('click', '.dropdown-item[data-exp]', function(e) {
        e.preventDefault();
        var exp = $(this).data('exp');
        if (table && table.button) { table.buttons(exp + ':name').trigger(); }
    });

    $('#ctxMenuLog').on('click', '.ctx-menu-item[data-ctx-action]', function() {
        var action = $(this).data('ctx-action');
        if (['excel','csv','pdf','print','copy'].indexOf(action) !== -1) {
            table.buttons(action + ':name').trigger();
        }
    });

    if ($('#tbl_log thead tr').length > 1) {
        $('#tbl_log thead tr')[1].cells[0].innerHTML = '';
    }
}
