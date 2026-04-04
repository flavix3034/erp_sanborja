<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

if(!isset($desde)){ $desde = "null"; }
if(!isset($hasta)){ $hasta = "null"; }
if(!isset($tipo))  { $tipo  = "null"; }
?>

<style type="text/css">
    div.ColVis { float: left; }
    .num-right { text-align: right !important; }
    .margen-alto  { color: #27ae60; font-weight: bold; }
    .margen-medio { color: #f39c12; }
    .margen-bajo  { color: #e74c3c; }
</style>

<section class="content" style="margin-left: 10px; margin-right: 10px;">

    <!-- FILTROS -->
    <div class="row" style="display:flex; flex-wrap:wrap;">
        <div class="col-sm-4 col-md-3">
            <div class="form-group">
                <label>Desde:</label>
                <input type="date" name="desde" id="desde" class="form-control" value="<?= $desde == 'null' ? '' : $desde ?>">
            </div>
        </div>

        <div class="col-sm-4 col-md-3">
            <div class="form-group">
                <label>Hasta:</label>
                <input type="date" name="hasta" id="hasta" class="form-control" value="<?= $hasta == 'null' ? '' : $hasta ?>">
            </div>
        </div>

        <div class="col-sm-4 col-md-2">
            <div class="form-group">
                <label>Tipo:</label>
                <?php
                    $ar_tipo = array('' => 'Todos', 'P' => 'Productos', 'S' => 'Servicios');
                    $sel_tipo = ($tipo == 'null') ? '' : $tipo;
                    echo form_dropdown('tipo', $ar_tipo, $sel_tipo, 'class="form-control" id="tipo"');
                ?>
            </div>
        </div>

        <div id="preparo" class="col-sm-1" style="margin: auto;">
            <br><a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>
        </div>
        <div id="refresco" class="col-sm-1"></div>
    </div>

    <!-- TABLA -->
    <div class="row" id="grilla">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
            <table id="tbl_top" class="display" style="width:100%; font-size: 12px; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <!-- producto, code, categoria, unidades, ventas, costos, ganancia, margen_pct -->
                        <th>Producto</th>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th>Unidades</th>
                        <th>Ventas (S/)</th>
                        <th>Costos (S/)</th>
                        <th>Ganancia (S/)</th>
                        <th>Margen %</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th><th></th><th></th>
                        <th></th><th></th><th></th>
                        <th></th><th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12" style="color:rgb(0,130,0); font-weight:bold; margin-top:10px; border:1px solid gray; padding:8px; font-size:12px;">
            Nota: Ventas, Costos y Ganancia están expresados sin IGV. El margen % = Ganancia / Ventas × 100.
            Los productos/servicios sin costo registrado aparecerán con costo 0.
        </div>
    </div>

</section>

<script type="text/javascript">

    $(document).ready(function() {

        var table = $('#tbl_top').DataTable({
            dom:            "Bfrtip",
            scrollY:        "480px",
            scrollX:        true,
            scrollCollapse: true,
            paging:         false,
            autoWidth:      false,
            buttons: [
                { extend: 'copyHtml5',  footer: true },
                { extend: 'excelHtml5', footer: true },
                { extend: 'csvHtml5',   footer: true },
                { extend: 'pdfHtml5',   footer: true, orientation: 'landscape', pageSize: 'A4',
                    exportOptions: { columns: [0,1,2,3,4,5,6,7] }
                },
                { extend: 'colvis', text: 'Columnas' }
            ],
            "ajax": "<?= base_url("reportes/get_top_productos/{$desde}/{$hasta}/{$tipo}") ?>",
            "order": [[6, "desc"]],
            "columnDefs": [
                { className: "num-right", targets: [3,4,5,6,7] },
                { width: "30%", targets: 0 },
                { width: "8%",  targets: 1 },
                { width: "14%", targets: 2 },
                { width: "8%",  targets: 3 },
                { width: "10%", targets: 4 },
                { width: "10%", targets: 5 },
                { width: "10%", targets: 6 },
                { width: "10%", targets: 7 }
            ],
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();
                var intVal = function(i) {
                    return typeof i === 'string' ? i.replace(/[,]/g, '') * 1 : (typeof i === 'number' ? i : 0);
                };

                // Totales solo en Ventas (4), Costos (5) y Ganancia (6)
                [4, 5, 6].forEach(function(col) {
                    var total = api.column(col).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
                    $(api.column(col).footer()).html('S/ ' + total.toFixed(2));
                });
            },
            "createdRow": function(row, data, index) {
                var margen = parseFloat(data[7]);
                if (margen >= 30) {
                    $('td:eq(7)', row).addClass('margen-alto');
                } else if (margen >= 10) {
                    $('td:eq(7)', row).addClass('margen-medio');
                } else {
                    $('td:eq(7)', row).addClass('margen-bajo');
                }
            }
        });

    });

    function activo1(){
        var desde = document.getElementById("desde").value || 'null';
        var hasta = document.getElementById("hasta").value || 'null';
        var tipo  = document.getElementById("tipo").value  || 'null';

        document.getElementById('refresco').innerHTML =
            '<a href="<?= base_url() ?>reportes/top_productos/' + desde + '/' + hasta + '/' + tipo + '" id="enlace_top">Ejecutar</a>';
        document.getElementById('preparo').style.display = "none";
        document.getElementById('enlace_top').click();
    }

</script>
