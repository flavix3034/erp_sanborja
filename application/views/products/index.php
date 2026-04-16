<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>
<?php
    $store_id = (isset($store_id) ? $store_id : "0");
    $categoria = (isset($categoria) ? $categoria : "0");
    $tipo = (isset($tipo) ? $tipo : "P");
?>

<section class="content" style="min-height: 100px;">
    <!-- ****** INICIO DE LOS FILTROS ********* -->
    <div class="row" style="display:flex; margin-top:10px">
        
        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div id="refresco"></div>
            <div class="form-group">
                <label for="">Tienda:</label>
                <?php
                    $group_id = $_SESSION["group_id"];
                    $q = $this->db->get('tec_stores');

                    $ar = array();
                    if ($group_id == '1'){
                        
                        foreach($q->result() as $r){
                            $ar[$r->id] = $r->state;
                        }
                    }else{
                        foreach($q->result() as $r){
                            if($r->id == $_SESSION["store_id"]){
                                $ar[$r->id] = $r->state;
                            }
                        }
                    }
                    echo form_dropdown('store_id', $ar, $store_id, 'class="form-control tip" id="store_id" required="required" style="font-size:14px; font-weight:bold;"');
                ?>
            </div>
        </div>
    
        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Tipo:</label>
                <?php
                    $ar = array();
                    $ar['P'] = 'Producto';
                    $ar['S'] = 'Servicio';
                    
                    echo form_dropdown('tipo', $ar, $tipo, 'class="form-control tip" id="tipo" required="required" style="font-size:14px; font-weight:bold;"');
                ?>
            </div>
        </div>


        <div class="col-sm-2" style="border-style:none; border-color:red;">
            <div class="form-group">
                <label for="">Categoria:</label>
                <?php
                
                    $this->db->select('id, name');
                    $this->db->from('tec_categories');
                    $q = $this->db->get();

                    $ar = array();
                    $ar[] = "";
                    foreach($q->result() as $r){
                        $ar[$r->id] = $r->name;
                    }
                    echo form_dropdown('categoria', $ar, $categoria, 'class="form-control tip" id="categoria" required="required" style="font-size:14px; font-weight:bold;"');
                ?>
            </div>
        </div>

        <div id="preparo" class="col-sm-2" style="border-style:none; border-color:red; margin: 20px 0px 20px 0px;">
            <div class="row">
                <div class="col-sm-5" style="padding:5px 0px 0px 0px; text-align: center;">
                    <button onclick="activo1()" class="btn btn-danger" style="">Buscar</button>
                </div>
                <div class="col-sm-5" style="padding:5px 0px 0px 0px; text-align: center">
                    <button onclick="limpiar()" class="btn btn-warning">Limpiar</button>
                </div>
            </div>
        </div>

    </div>
</section>

<section class="content">
    <!-- id, code, name, category_id, unidad, alert_cantidad, price -->
    <div class="row" id="grilla">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <table id="example" class="display" style="width:100%; font-size: 12px; margin-bottom: 20px;" data-page-length='16'>
                <thead>
                    <tr>
                        <!-- id, code, name, category_id, unidad, alert_cantidad, price -->
                        <th style="max-width:30px !important">id</th>
                        <th>Codigo</th>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Marca</th>
                        <th>Alerta<br>Cantidad</th>
                        <th>Precio</th>
                        <th>Precio<br>xMayor</th>

                        <th>Costo</th>
                        <th>.</th>
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
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>
<div id="pixar"></div>
<script type="text/javascript">
    
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print'],
            "ajax": "<?= base_url("products/getProducts/{$store_id}/{$tipo}/{$categoria}") ?>",
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                
                //alert("Enterogermina");
                // Remove the formatting to get integer data for summation
                /*var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
     
                // Total over all pages
                total = api
                    .column( 4 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );*/
                
            }
            //"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ){
            //    if (aData[10] == ''){ $('td', nRow).css('background-color', 'rgb(170,170,170)'); }
            //},

        });

    });

    function activo1(){
        var store_id    = document.getElementById("store_id").value
        var categoria   = document.getElementById("categoria").value
        var tipo        = document.getElementById("tipo").value
        //console.log("store_id:"+store_id)
        //console.log("categoria:"+categoria)
        if( store_id.length == 0){ store_id = '0'}
        if( categoria.length == 0){ categoria = '0'}
        if( tipo.length == 0){ tipo = 'P'}
        var cadena = "/" + store_id + "/" + tipo + "/" + categoria
        console.log(cadena)
        document.getElementById("pixar").innerHTML = '<a href="<?= base_url("products/index") ?>' + cadena + '" id="pixor">xx</a>'
        //+ String(store_id) + "/" + String(categoria) + '" id=\"pixor\">xx</a>'
        document.getElementById("pixor").click()
    }

    function limpiar(){
        document.getElementById("store_id").value   = ""
        document.getElementById("categoria").value  = ""
        document.getElementById("tipo").value       = "P"
    }

    function eliminar(id){
        if (confirm("¿Confirme que desea eliminar?")){
            $.ajax({
                data    : {id:id},
                url     : '<?= base_url("products/eliminar") ?>',
                type    : "post",
                success : function(res){
                    let obj = JSON.parse(res)
                    alert(obj.msg)
                    location.reload()
                }
            })
        }
    }

    function anular(id){
        if (confirm("¿Confirme que desea anular?")){
            $.ajax({
                data    : {id:id},
                url     : '<?= base_url("products/anular") ?>',
                type    : "post",
                success : function(res){
                    let obj = JSON.parse(res)
                    alert(obj.msg)
                    location.reload()
                }
            })
        }
    }

    function editar(id){
        window.location.href = "<?= base_url("products/add/") ?>" + id
    }

    function printEtiqueta(product_id, variant_id) {
        window.open('<?= base_url("products/print_etiqueta/") ?>' + product_id + '/' + variant_id, '_blank');
    }
</script>