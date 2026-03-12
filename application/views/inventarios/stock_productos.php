<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    if($query_stock == false){
        echo("No existe el inventario x default, cambie el Inventario vigente en Configuracion.<br>");
        die("<a href=\"" . base_url("welcome/cierra_sesion") . "\">Regresar</a>");
    }
?>

<div class="row" style="margin-top:10px;">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
        <div class="table-responsive">
            <table id="tabla_stock_avanzado" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:30px;"></th>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Cant. M&iacute;nima</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($query_stock->result() as $r):
                        $tiene_variantes = isset($variantes_map[$r->id]);
                        $stock_val = $r->stock * 1;
                        $clase_stock = ($stock_val < $r->alert_cantidad) ? 'color:red;font-weight:bold;' : '';
                    ?>
                    <tr data-pid="<?= $r->id ?>">
                        <td class="text-center">
                            <?php if($tiene_variantes): ?>
                            <button class="btn btn-xs btn-default btn-toggle-var" data-pid="<?= $r->id ?>" title="Ver variantes" style="padding:2px 6px;">
                                <i class="fa fa-plus"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                        <td><?= $r->name ?></td>
                        <td class="text-right" style="<?= $clase_stock ?>"><?= $stock_val ?></td>
                        <td class="text-right" style="color:rgb(120,120,120);"><?= $r->alert_cantidad * 1 ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
// Datos de variantes en JS
var variantesMap = <?php
    $js_map = array();
    foreach($variantes_map as $pid => $vars){
        $js_map[$pid] = array();
        foreach($vars as $v){
            $js_map[$pid][] = array('name' => $v->name, 'stock' => $v->stock * 1);
        }
    }
    echo json_encode($js_map);
?>;

$(document).ready(function(){
    $('#tabla_stock_avanzado').DataTable({
        "paging": false,
        "ordering": true,
        "info": false,
        "columnDefs": [{ "orderable": false, "targets": 0 }],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        }
    });

    $(document).on('click', '.btn-toggle-var', function(){
        var btn = $(this);
        var pid = btn.data('pid');
        var icon = btn.find('i');
        var parentRow = btn.closest('tr');

        // Si ya están visibles, removerlas
        if (icon.hasClass('fa-minus')) {
            parentRow.nextUntil('tr:not(.variante-row)').remove();
            icon.removeClass('fa-minus').addClass('fa-plus');
            return;
        }

        // Insertar filas de variantes justo debajo
        var vars = variantesMap[pid] || [];
        var html = '';
        for (var i = 0; i < vars.length; i++) {
            html += '<tr class="variante-row" style="background-color:#f0f7ff;">'
                + '<td></td>'
                + '<td style="padding-left:30px;"><i class="fa fa-caret-right" style="color:#999;margin-right:6px;"></i>' + vars[i].name + '</td>'
                + '<td class="text-right">' + vars[i].stock + '</td>'
                + '<td></td>'
                + '</tr>';
        }
        parentRow.after(html);
        icon.removeClass('fa-plus').addClass('fa-minus');
    });
});
</script>
