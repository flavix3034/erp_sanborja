<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
    if(!isset($desde)){ $desde = "null"; }
    if(!isset($hasta)){ $hasta = "null"; }
    if(!isset($store_id)){ $store_id = $_SESSION["store_id"];}
?>
<style type="text/css">
    .filitas{
        margin-top: 10px;
    }
</style>
<script type="text/javascript">
    var store_id = <?= $store_id ?>;
    function activo1(){
        let desde = document.getElementById("desde").value
        let hasta = document.getElementById("hasta").value

        if(desde.length == 0){
            desde = 'null'
        }

        if(hasta.length == 0){
            hasta = 'null'
        }

        document.getElementById('refresco').innerHTML = '<a href="<?= base_url() ?>compras/index/' + store_id + '/' + desde + '/' + hasta + '" id="enlace_grilla_compras">Ejecutar</a>'
        document.getElementById('preparo').style.display = "none"
        document.getElementById('enlace_grilla_compras').click()
    }
</script>
<section class="content">

    <div class="row" style="display:flex;margin-top: 15px; margin-bottom: 5px;">
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
        
        <div id="preparo" class="col-sm-2 col-md-1" style="border-style:none; border-color:red; margin: auto;">
            <br><a href="#" onclick="activo1()" class="btn btn-primary"><b>Consultar</b></a>&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        
        <div id="refresco" class="col-sm-1"></div>
    </div>

    <div class="row" id="grilla">
        <!-- date, date_ingreso, subtotal, igv, total, supplier_id, created_by, store_id, tipoDoc, nroDoc -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table id="example" class="display" style="width:90%; font-size: 12px; margin-bottom: 20px;" data-page-length='12'>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Tienda</th>
                    <th>Fecha<br>Pago</th>
                    <th>Fecha<br>ingreso</th>
                    <th>TipoDoc</th>
                    <th>NroDoc</th>
                    <th>Proveedor</th>
                    <th>Creado por</th>
                    <th>Total</th>
                    <th>Actions</th>
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
                    <th></th>
                    <th>.</th>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>

  <!--*** FORMULARIO MODAL POPUP BOOTSTRAP ****-->
  <h2></h2>
  <!-- Trigger the modal with a button -->
  <span id="btn_ver" data-toggle="modal" data-target="#myModal"></span>

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="titulo_modal_1">Compra</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          
        </div>
        <div class="modal-body" id="body_modal_1">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


    <div class="row filitas">
        <div class="col-xs-12 col-md-10 col-lg-9" id="pizarra">
        </div>
    </div>

</section>

<script type="text/javascript">
    
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            "ajax": "<?= base_url("compras/get_compras/{$store_id}/{$desde}/{$hasta}") ?>",
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
     
                // Total over all pages
                total = api
                    .column( 8 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                pageTotal = api
                    .column( 8, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                
                // Update footer
                $( api.column( 8 ).footer() ).html(
                    'S/ '+pageTotal.toFixed(2) +'<br>(S/ '+ total.toFixed(2) +')'
                );
            },
            "columnDefs":[
                { className: "dt-right", "targets": [8]}
            ]

        });
    });

    function editar(id){
        window.location.href = '<?= base_url("compras/add/") ?>' + id
    }

    function ver(id){
        $.ajax({
            data    :{id:id},
            type    :"get",
            url     :"<?= base_url("compras/ver") ?>",
            success :function(res){
                document.getElementById("titulo_modal_1").innerHTML = "Compra Id:" + id
                document.getElementById("body_modal_1").innerHTML = res

                //document.getElementById("pizarra").innerHTML = res
                document.getElementById("btn_ver").click()
            }
        })
    }

    function eliminar(id){
        if(confirm("Estás a punto de eliminar una compra. Ten en cuenta que esta acción puede tener consecuencias importantes en tu inventario y registros de ventas")){
            if (confirm("Desea eliminar?")){
                $.ajax({
                    data    :{id:id},
                    type    :"get",
                    url     :"<?= base_url("compras/eliminar") ?>",
                    success :function(res){
                        var obj = JSON.parse(res)
                        if (obj.rpta_msg == "success"){
                            //alert("Se logra eliminar dicho Movimiento.")
                            alert(obj.message)
                            location.reload()
                        }else{
                            alert(obj.message) // "No se puede eliminar..."
                        }
                    }
                })
            }
        }
    }
</script>