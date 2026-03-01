<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 

    if(!isset($desde)){ $desde = "null"; }
    if(!isset($hasta)){ $hasta = "null"; }
    if(!isset($store_id)){ 
        $store_id = $_SESSION["store_id"]; 
    }else{
        if($store_id == 'null'){
            $store_id = $_SESSION["store_id"];    
        }
    }
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
        <div class="col-sm-11 col-md-10 col-lg-9">
            <div class="row">
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
                
                <div class="col-sm-4 col-md-3" style="border-style:none; border-color:red;">
                    <div class="form-group">
                        <label for="">Tienda:</label>
                        <?php
                            //$group_id = $_SESSION["group_id"];
                            $q = $this->db->get('tec_stores');

                            $ar = array();
                            //if ($group_id == '1'){
                            if($Admin){
                                $ar[] = "Todas";
                                foreach($q->result() as $r){
                                    $ar[$r->id] = $r->name;
                                }
                            }else{
                                foreach($q->result() as $r){
                                    if($r->id == $_SESSION["store_id"]){
                                        $ar[$r->id] = $r->name;
                                    }
                                }
                            }
                            echo form_dropdown('store_id', $ar, $store_id, 'class="form-control tip" id="store_id" required="required"');
                        ?>
                    </div>
                </div>

                <div id="preparo" class="col-sm-2" style="border-style:none; border-color:red; padding-top:26px;text-align:right!important">
                    <a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>
                </div>
                
                <div id="refresco"></div>
            </div>
        </div>
    </div>

    <!-------- SECCION GRILLAS ------------------------------------->
    <div class="row" id="grilla">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <table id="example" class="display" style="font-size: 12px; width: 100%; margin-bottom: 20px;" data-page-length='9'>
                <thead>
                    <tr>
                        <!-- id, fecha, tipodoc, serie, numero, tipo_cli, ruc, nombres, base_imponible, igv, importe, moneda, estado
                        filtros: no anulados, no tickets -->
                        <th>Id</th>
                        <th>Fecha</th>
                        <th>Tipo_doc</th>
                        <th>Serie</th>
                        <th>Numero</th>

                        <th>Ruc</th>
                        <th>Nombres</th>
                        <th>Monto_Base</th>
                        <th>IGV</th>
                        <th>Importe</th>

                        <th>Moneda</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>.</th>
                        <th>.</th>
                        <th>.</th>
                        <th>.</th>
                        <th>.</th>

                        <th>.</th>
                        <th>.</th>
                        <th>.</th>
                        <th>.</th>
                        <th>.</th>
                        
                        <th>.</th>
                        <th>.</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div>
        <!-- Trigger the modal with a button -->
        <span id="myBtn"></span>

        <!-- Modal -->
        <div class="modal fade" id="pizarra" role="dialog">
            <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <!--<h4 class="modal-title">Modal Header</h4>-->
                </div>
                <div class="modal-body">
                  <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
              
            </div>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-sm-12" style="color:rgb(0,170,0); font-weight: bold; margin-top:15px;border-style: solid; border-color:gray; border-width:1px; padding: 10px;">
        Nota.- Tanto la columna Ventas como la columna Costos están sin incluir IGV.
    </div>
</div>

<script type="text/javascript">
    
    $(document).ready(function() {
        $('#example').DataTable({
            dom:            "Bfrtip",
            /*scrollY:        "450px",
            scrollX:        true,
            scrollCollapse: true,
            paging:         false,
            buttons:        [   { extend: 'copyHtml5', footer: true },
                                { extend: 'excelHtml5', footer: true },
                                { extend: 'csvHtml5', footer: true },
                                { extend: 'pdfHtml5', footer: true, orientation: 'landscape', pageSize: 'A4', 'footer': true,
                                    exportOptions: { columns: [ 0, 1, 2] } 
                                }, 
                                { extend: 'colvis', text: 'Filtro'} 
                            ],*/
            //fixedColumns:   { left: 2},
            "ajax": "<?= base_url("reportes/get_contabilidad_sales/{$desde}/{$hasta}/{$store_id}") ?>"
        });

        /*$("#myBtn").click(function(){
            $("#pizarra").modal();
        });*/

    });

    function activo1(){
        let desde = document.getElementById("desde").value
        let hasta = document.getElementById("hasta").value
        let store_id = document.getElementById("store_id").value
        
        if(desde.length == 0){           desde = 'null'       }
        if(hasta.length == 0){           hasta = 'null'       }
        if(store_id.length == 0){        store_id = 'null'    }

        document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>reportes/contabilidad/' + desde + '/' + hasta + '/' + store_id + '" id="enlace_grilla_compras">Ejecutar</a>'
        document.getElementById('preparo').style.display = "none"
        document.getElementById('enlace_grilla_compras').click()
    }
</script>
<style type="text/css">
    /*.table td:nth-child(2) { text-align: right;}*/
</style>