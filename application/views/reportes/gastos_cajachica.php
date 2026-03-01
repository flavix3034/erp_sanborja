<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

    if(!isset($desde)){ $desde = "null"; }
    if(!isset($hasta)){ $hasta = "null"; }
?>

<style type="text/css">
    div.ColVis {
        float: left;
    }
    .suma_fila{
        text-align: right;
        font-weight: bold;
    }
</style>

<section class="content" style="margin: 15px 0px 10px 20px;">

    <!-------------- SECCION DE FILTROS ------------------------>
    <div class="row" style="display:flex;">
        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Desde:</label>
                <input type="date" name="desde" id="desde" class="form-control" value="<?= $desde ?>">
            </div>
        </div>

        <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Hasta:</label>
                <input type="date" name="hasta" id="hasta" class="form-control" value="<?= $hasta ?>">
            </div>
        </div>

        <div id="preparo" class="col-sm-1" style="border-style:none; border-color:red; margin: auto;">
            <br><a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        </div>

        <div id="refresco" class="col-sm-1"></div>
    </div>

    <!-------- SECCION GRILLAS ------------------------------------->
    <div class="row" id="grilla">
        <div class="col-xs-12 col-sm-12 col-md-11 col-lg-10">
            <table id="tbl_gastos_cc" class="display" style="font-size: 12px; width: 100%; margin-bottom: 20px;" data-page-length='25'>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Categor&iacute;a</th>
                        <th>Descripci&oacute;n</th>
                        <th>Beneficiario</th>
                        <th>Tipo Doc.</th>
                        <th>Serie-N&uacute;mero</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</section>

<script type="text/javascript">

    $(document).ready(function() {
        $('#tbl_gastos_cc').DataTable({
            dom: "Bfrtip",
            buttons: [
                { extend: 'copyHtml5', footer: true },
                { extend: 'excelHtml5', footer: true, title: 'Gastos_CajaChica' },
                { extend: 'csvHtml5', footer: true, title: 'Gastos_CajaChica' },
                { extend: 'pdfHtml5', footer: true, orientation: 'landscape', pageSize: 'A4', title: 'Gastos de Caja Chica',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] }
                },
                { extend: 'colvis', text: 'Filtro'}
            ],

            "ajax": "<?= base_url("reportes/get_gastos_cajachica/{$desde}/{$hasta}") ?>",
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;

                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total monto (columna 7)
                total = api.column(7).data().reduce( function (a, b){return intVal(a) + intVal(b);}, 0);

                pageTotal = api.column(7, { page: 'current'}).data().reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

                $( api.column(7).footer() ).html('S/ '+ total.toFixed(2));
            },

            "columnDefs":[
                { className: "suma_fila", "aTargets": [7] }
            ]

        });

    });

    function activo1(){
        let desde = document.getElementById("desde").value;
        let hasta = document.getElementById("hasta").value;

        if(desde.length == 0){ desde = 'null'; }
        if(hasta.length == 0){ hasta = 'null'; }

        document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>reportes/gastos_cajachica/' + desde + '/' + hasta + '" id="enlace_grilla_compras">Ejecutar</a>';
        document.getElementById('preparo').style.display = "none";
        document.getElementById('enlace_grilla_compras').click();
    }
</script>
