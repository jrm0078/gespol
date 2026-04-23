"use strict";

$(".preloader2").fadeIn();

Cargar();
$(".preloader2").fadeOut();

function Cargar() { CargaTabla(); }

function CargaTabla() {
    $('#tbl_agentes').DataTable().destroy();

    if ($('#tbl_agentes thead tr').length < 2) {
        $('#tbl_agentes thead tr').clone(true).appendTo('#tbl_agentes thead');
    }
    $('#tbl_agentes thead tr:eq(1) th').each(function(i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="Buscar ' + title + '" />');
        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });
    });

    var table = $('#tbl_agentes').DataTable({
        "sDom": '<f>t<pl>',
        "bPaginate": true, "bLengthChange": true, "bFilter": true, "bInfo": false, "bAutoWidth": false,
        "pageLength": 50, "orderCellsTop": true, "fixedHeader": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "language": {
            "emptytable": "No hay agentes", "infoEmpty": "Sin resultados",
            "infoFiltered": "(Filtrando _MAX_)", "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...", "processing": "Procesando...", "search": "Buscar:",
            "zeroRecords": "Sin resultados",
            "paginate": { "previous": "Anterior", "sNext": "Siguiente" }
        },
        "columnDefs": [{ "targets": 0, "visible": false, "searchable": false }],
        "processing": true, "serverSide": true, "order": [[1, "asc"]],
        "buttons": [
            { extend: 'excel', text: 'Excel' }, { extend: 'csv', text: 'CSV' },
            { extend: 'pdf', text: 'PDF' }, { extend: 'print', text: 'Imprimir' }, { extend: 'copy', text: 'Copiar' }
        ],
        "ajax": { type: "POST", data: {}, url: "inc/func_ajax.php/CargatablaAgentes", dataType: 'JSON' }
    });

    initTablaToolbar({
        tableId:   '#tbl_agentes',
        ctxMenuId: '#ctxMenuAgentes',
        btnAdd:    '#btnTbAddAgente',
        btnEdit:   '#btnTbEditAgente',
        getDt:     function() { return $('#tbl_agentes').DataTable(); },
        onAdd:     function() { window.localStorage.setItem('pag_id1', ''); _recargarTab('fichaagente.php', 'Agente', 'fas fa-user-shield'); },
        onEdit:    function(tr) {
            var data = $('#tbl_agentes').DataTable().row(tr).data();
            if (data) { window.localStorage.setItem('pag_id1', data[0]); _recargarTab('fichaagente.php', 'Agente', 'fas fa-user-shield'); }
        }
    });

    if ($('#tbl_agentes thead tr').length > 1) {
        $('#tbl_agentes thead tr')[1].cells[0].innerHTML = '';
    }
}
