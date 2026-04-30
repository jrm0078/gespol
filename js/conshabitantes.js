"use strict";

$(".preloader2").fadeIn();
Cargar();
$(".preloader2").fadeOut();

function Cargar() { CargaTabla(); }

function CargaTabla() {
    $('#tbl_habitantes').DataTable().destroy();

    if ($('#tbl_habitantes thead tr').length < 2) {
        $('#tbl_habitantes thead tr').clone(true).appendTo('#tbl_habitantes thead');
    }
    $('#tbl_habitantes thead tr:eq(1) th').each(function(i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="Buscar ' + title + '" />');
        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) table.column(i).search(this.value).draw();
        });
    });

    var table = $('#tbl_habitantes').DataTable({
        "sDom": '<f>t<pl>',
        "bPaginate": true, "bLengthChange": true, "bFilter": true, "bInfo": false, "bAutoWidth": false,
        "pageLength": 50, "orderCellsTop": true, "fixedHeader": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "language": {
            "emptytable": "No hay habitantes", "infoEmpty": "Sin resultados",
            "lengthMenu": "Mostrar _MENU_ registros", "search": "Buscar:",
            "zeroRecords": "Sin resultados",
            "paginate": { "previous": "Anterior", "sNext": "Siguiente" }
        },
        "columnDefs": [{ "targets": 0, "visible": false, "searchable": false }],
        "processing": true, "serverSide": true, "order": [[3, "asc"], [4, "asc"]],
        "buttons": [
            { extend: 'excel', text: 'Excel' }, { extend: 'csv', text: 'CSV' },
            { extend: 'pdf', text: 'PDF' }, { extend: 'print', text: 'Imprimir' }, { extend: 'copy', text: 'Copiar' }
        ],
        "ajax": { type: "POST", data: {}, url: "inc/func_ajax.php/CargatablaHabitantes", dataType: 'JSON' }
    });

    initTablaToolbar({
        tableId:   '#tbl_habitantes',
        ctxMenuId: '#ctxMenuHabitantes',
        btnAdd:    '#btnTbAddHabitante',
        btnEdit:   '#btnTbEditHabitante',
        getDt:     function() { return $('#tbl_habitantes').DataTable(); },
        onAdd:     function() {
            window.localStorage.setItem('pag_id1', '');
            if (window._gTabs && window._gTabs.find(function(t){ return t.pagina==='fichahabitante.php'; })) { _recargarTab('fichahabitante.php', 'Habitante', 'fas fa-user'); }
            else { CargarPagina('fichahabitante.php', 'Habitante', 'fas fa-user'); }
        },
        onEdit:    function(tr) {
            var data = $('#tbl_habitantes').DataTable().row(tr).data();
            if (data) {
                window.localStorage.setItem('pag_id1', data[0]);
                if (window._gTabs && window._gTabs.find(function(t){ return t.pagina==='fichahabitante.php'; })) { _recargarTab('fichahabitante.php', 'Habitante', 'fas fa-user'); }
                else { CargarPagina('fichahabitante.php', 'Habitante', 'fas fa-user', data[0]); }
            }
        }
    });

    if ($('#tbl_habitantes thead tr').length > 1) {
        $('#tbl_habitantes thead tr')[1].cells[0].innerHTML = '';
    }
}
