"use strict";

$(".preloader2").fadeIn();
Cargar();
$(".preloader2").fadeOut();

function Cargar() { CargaTabla(); }

function CargaTabla() {
    $('#tbl_servicios').DataTable().destroy();

    if ($('#tbl_servicios thead tr').length < 2) {
        $('#tbl_servicios thead tr').clone(true).appendTo('#tbl_servicios thead');
    }
    $('#tbl_servicios thead tr:eq(1) th').each(function(i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="Buscar ' + title + '" />');
        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) table.column(i).search(this.value).draw();
        });
    });

    var table = $('#tbl_servicios').DataTable({
        "sDom": '<"toolbar-dt"B>f<t><pl>',
        "dom":  '<"toolbar-dt"B>f<t><pl>', "bPaginate": true, "bLengthChange": true, "bFilter": true,
        "bInfo": false, "bAutoWidth": false, "pageLength": 50, "orderCellsTop": true, "fixedHeader": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "language": {
            "emptytable": "No hay servicios", "infoEmpty": "Sin resultados",
            "lengthMenu": "Mostrar _MENU_ registros", "search": "Buscar:",
            "zeroRecords": "Sin resultados", "paginate": { "previous": "Anterior", "sNext": "Siguiente" }
        },
        "columnDefs": [{ "targets": 0, "visible": false, "searchable": false }],
        "processing": true, "serverSide": true, "order": [[2, "desc"]],
        "buttons": [
            { extend: 'excel', text: 'Excel' }, { extend: 'csv', text: 'CSV' },
            { extend: 'pdf', text: 'PDF' }, { extend: 'print', text: 'Imprimir' }, { extend: 'copy', text: 'Copiar' }
        ],
        "ajax": { type: "POST", data: {}, url: "inc/func_ajax.php/CargatablaServicios", dataType: 'JSON' }
    });

    initTablaToolbar({
        tableId:   '#tbl_servicios',
        ctxMenuId: '#ctxMenuServicios',
        btnAdd:    '#btnTbAddServicio',
        btnEdit:   '#btnTbEditServicio',
        getDt:     function() { return $('#tbl_servicios').DataTable(); },
        onAdd:     function() {
            window.localStorage.setItem('pag_id1', '');
            if (window._gTabs && window._gTabs.find(function(t){ return t.pagina==='fichaservicio.php'; })) { _recargarTab('fichaservicio.php', 'Servicio', 'fas fa-calendar-alt'); }
            else { CargarPagina('fichaservicio.php', 'Servicio', 'fas fa-calendar-alt'); }
        },
        onEdit:    function(tr) {
            var data = $('#tbl_servicios').DataTable().row(tr).data();
            if (data) {
                window.localStorage.setItem('pag_id1', data[0]);
                if (window._gTabs && window._gTabs.find(function(t){ return t.pagina==='fichaservicio.php'; })) { _recargarTab('fichaservicio.php', 'Servicio', 'fas fa-calendar-alt'); }
                else { CargarPagina('fichaservicio.php', 'Servicio', 'fas fa-calendar-alt', data[0]); }
            }
        }
    });

    if ($('#tbl_servicios thead tr').length > 1) {
        $('#tbl_servicios thead tr')[1].cells[0].innerHTML = '';
    }
}
