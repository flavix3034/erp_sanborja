<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<section class="content" style="margin-left: 15px; margin-right: 15px;">

    <div class="row" style="margin-bottom: 12px;">
        <div class="col-sm-12">
            <a href="<?= base_url('proveedores/add') ?>" class="btn btn-primary btn-sm">
                <i class="glyphicon glyphicon-plus"></i> Nuevo Proveedor
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12" style="overflow: auto;">
            <table id="tbl_proveedores" class="display" style="width:100%; font-size: 12px;">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Nombre / Razón Social</th>
                        <th>RUC</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Tel. Adicional</th>
                        <th>Contacto</th>
                        <th>Dirección</th>
                        <th>Notas / Especialidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</section>

<!-- Modal detalle proveedor -->
<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#4e73df; color:#fff; border-radius:4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1;">&times;</button>
                <h4 class="modal-title" id="modalDetalleTitulo" style="font-size:15px;"></h4>
            </div>
            <div class="modal-body" id="modalDetalleBody" style="padding: 16px;">
                <p class="text-center"><i class="glyphicon glyphicon-refresh"></i> Cargando...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#tbl_proveedores').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy',  text: 'Copiar' },
            { extend: 'csv',   text: 'CSV' },
            { extend: 'excel', text: 'Excel' },
            { extend: 'pdf',   text: 'PDF', orientation: 'landscape', pageSize: 'A4',
              exportOptions: { columns: [1,2,3,4,5,6,7,8] } },
            { extend: 'print', text: 'Imprimir',
              exportOptions: { columns: [1,2,3,4,5,6,7,8] } },
            { extend: 'colvis', text: 'Columnas' }
        ],
        ajax: '<?= base_url("proveedores/getProveedores") ?>',
        columnDefs: [
            { visible: false, targets: 0 },
            { orderable: false, targets: 9 }
        ],
        language: {
            search:      'Buscar:',
            lengthMenu:  'Mostrar _MENU_ registros',
            info:        'Mostrando _START_ a _END_ de _TOTAL_ proveedores',
            infoEmpty:   'Sin registros',
            zeroRecords: 'No se encontraron resultados',
            paginate: { first: 'Primero', last: 'Último', next: 'Sig.', previous: 'Ant.' }
        },
        pageLength: 15
    });
});

function verDetalle(id, nombre) {
    $('#modalDetalleTitulo').text(nombre);
    $('#modalDetalleBody').html('<p class="text-center"><i class="glyphicon glyphicon-refresh"></i> Cargando...</p>');
    $('#modalDetalle').modal('show');
    $.ajax({
        url: '<?= base_url("proveedores/ver/") ?>' + id,
        type: 'GET',
        success: function(html) {
            $('#modalDetalleBody').html(html);
        }
    });
}

function modificar(id) {
    window.location.href = '<?= base_url("proveedores/add") ?>?id=' + id;
}

function eliminar(id) {
    if (confirm('¿Confirma que desea eliminar este proveedor?')) {
        $.ajax({
            url:  '<?= base_url("proveedores/eliminar") ?>',
            type: 'GET',
            data: { id: id },
            success: function(r) {
                if (r == '0') {
                    $('#tbl_proveedores').DataTable().ajax.reload();
                } else {
                    alert('No se puede eliminar: el proveedor tiene compras registradas.');
                }
            }
        });
    }
}
</script>
