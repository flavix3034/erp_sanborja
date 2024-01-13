<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
    $id_compras         = isset($id) ? $id : ""; 

    $fecha               = isset($date) ? $date : "";
    $fecha_ingreso       = isset($date_ingreso) ? $date_ingreso : "";
    $nroDoc             = isset($nroDoc) ? $nroDoc : "";
    $tipoDoc            = isset($tipoDoc) ? $tipoDoc : "";
    $proveedor_id       = isset($proveedor_id) ? $proveedor_id : "";
    $redondeo           = isset($redondeo) ? $redondeo : "";
    $modo               = isset($modo) ? $modo : "I";
    if($modo == 'U'){
        $modo_edicion = "1";
    }else{
        $modo_edicion = "0";
    }

    ?>
    <script type="text/javascript">
        var ar_items    = new Array();
    </script>
    <?php

    // SOLO EN CASO DE EDITAR UNA COMPRA =====================
    if(strlen($id_compras."")>0){
        $cSql = "select * from tec_compras where id = ?";
        $query = $this->db->query($cSql,array($id_compras));

        foreach($query->result() as $r){
            $nroDoc         = $r->nroDoc;
            $tipoDoc        = $r->tipoDoc;
            $proveedor_id   = $r->proveedor_id;
            $redondeo       = $r->redondeo;
            $fecha          = $r->fecha;
            $fecha_ingreso  = $r->fecha_ingreso;
        }

        $cSql = "select * from tec_compra_items where compra_id = ?";
        $query = $this->db->query($cSql,array($id_compras));
        $ni = 0;
        foreach($query->result() as $r){
            
            if($ni == 0){
                echo "<script>\n";
            }
            
            //$costo = $r->precio_con_igv * 

            echo "ar_items.push({id:".$r->product_id.", 
            name:       '".$r->product_name."', 
            quantity:   ".$r->cantidad.", 
            cost:       ".$r->precio_sin_igv.", 
            subtotal:   ".($r->cantidad * 1 * $r->precio_sin_igv).",
            precio:     ".$r->precio_con_igv."})\n";

            /*
            echo "ar_items[$ni] = ['id' " . $r->product_id . ","
                . $r->product_id . ","
                . "'" . $r->product_name . "',"
                . $r->cantidad . ","
                . $r->precio_sin_igv . ","
                . $r->subtotal . ","
                . $r->precio_con_igv . "];\n";

            
            echo "ar_items['id']="          . $r->product_id . "\n";
            echo "ar_items['name']='"       . $r->product_name . "'\n";
            echo "ar_items['quantity']="    . $r->cantidad . "\n";
            echo "ar_items['cost']="        . $r->precio_sin_igv . "\n";
            echo "ar_items['subtotal']="    . $r->subtotal . "\n";
            echo "ar_items['precio']="      . $r->precio_con_igv . "\n";*/
            $ni++;
        }
        if($ni>0){
            echo "window.addEventListener('load', function() { cargar_items(); });";
            echo "</script>\n";
        }
    }
?>
<!--<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css'>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>-->

<style type="text/css">
    .filitas{
        margin-top: 10px;
        border-style: none;
        border-width:  1px;
    }
    .celdas_totales{
        background-color: rgb(160,160,160);
    }
    .table th{
        height: 35px;
        padding: 4px !important;
    }

</style>
<section class="content">

    <?php echo form_open_multipart(base_url("compras/save"), 'class="validation" id="form_compra" onsubmit="return guardar_compra()"'); ?>
    <div class="row filitas">
        <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3">
            <label>Fecha de Pago</label>
            <input type="datetime-local" name="date" id="date" value="<?= $fecha ?>" class="form-control">
        </div>
        <div class="col-xs-6 col-sm-5 col-md-4 col-lg-3">
            <label>Fecha Ingreso a Almacén</label>
            <input type="datetime-local" name="date_ingreso" id="date_ingreso" value="<?= $fecha_ingreso ?>" class="form-control">
        </div>
    </div>

    <div class="row filitas">
        <div class="col-xs-12 col-sm-3 col-lg-2">
            <label>Tipo Doc</label>
            <?php 
               $cSql = "select id, descrip from tec_tipos_doc order by id";
               $result = $this->db->query($cSql)->result_array();
               $ar_p = array();
               $ar_p[""] = "--- Seleccione ---";
               foreach($result as $r){
                    $ar_p[ $r["id"] ] = $r["descrip"];
               }

               echo form_dropdown('tipoDoc',$ar_p, $tipoDoc,'class="form-control tip" id="tipoDoc" required');
            ?>
        </div>

        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
            <label>Nro. Doc</label>
            <input type="text" name="nroDoc" id="nroDoc" value="<?= $nroDoc ?>" class="form-control" required>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
            <label>Proveedor</label>
            <?php 
               $cSql = "select id, nombre, ruc from tec_proveedores order by nombre";
               $result = $this->db->query($cSql)->result_array();
               $ar_p = array();
               $ar_p[""] = "--- Seleccione ---";
               foreach($result as $r){
                    $ar_p[ $r["id"] ] = $r["nombre"];
               }

               echo form_dropdown('proveedor_id',$ar_p, $proveedor_id,'class="form-control tip" id="proveedor_id" required');
            ?>
        </div>

    </div>

    <div class="row filitas">
        <div class="col-xs-6 col-sm-5 col-md-4">
            
            <?php
            /* 
               $cSql = "select id, code, name, price, unidad from tec_products order by name";
               $result = $this->db->query($cSql)->result_array();
               $ar_p = array();
               $ar_p[""] = "--- Seleccione Producto ---";
               foreach($result as $r){
                    $ar_p[ $r["id"] ] = $r["name"] . " (" . $r["unidad"] . ")";
               }

               echo form_dropdown('product_id',$ar_p,'','class="form-control tip" id="product_id" onchange="busqueda_precio(this)"');
            */
            ?>

            <div>
                <label>Producto</label><br>    
                <input type="text" name="campo" id="campo" class="form-control">

                <ul id="lista"></ul>
                <input type="hidden" name="product_id" id="product_id">
            </div>

            <script>
                function busqueda_precio(){
                    /*
                    var datin = document.getElementById("product_id").value
                    $.ajax({
                        data    : {dato1: datin, tipo_precio: document.getElementById('tipo_precio').checked == true ? 'por_mayor' : 'por_menor'},
                        url     : '<?= base_url("products/busqueda_precio") ?>',
                        type    : "post",
                        success : function(response){
                            document.getElementById("cost").value = response
                            document.getElementById("quantity").focus()
                        }
                    })*/
                }
            </script>

        </div>

        <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
            <div class="form-group">
                <label>Cantidad</label>
                <input type="text" name="quantity" id="quantity" class="form-control">
            </div>
        </div>

        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
            <div class="form-group">
                <label>Precio</label>
                <input type="text" name="cost" id="cost" class="form-control">
            </div>
        </div>

        <div class="col-xs-3 col-sm-1">
            <div class="row" style="margin-top:20px">
                <!--con IGV: <input type="checkbox" id="chk_igv" name="chk_igv" value="1">-->
                <button type="button" class="btn btn-success" onclick="agregar()" style="font-size: 18px; font.font-weight: bold;">+</button>
                <!--<button type="button" onclick="llenar()">llenar</button>-->
            </div>
        </div>

        <div class="col-xs-2 col-sm-1 cod-md-1" style="border-style:none; border-color:blue">
            <label>IGV:</label><br>
            <input type="checkbox" id="chk_igv" name="chk_igv" checked>
        </div>

        <div class="col-xs-2 col-sm-2" style="border-style:dotted; border-color:gray">
            <label><b><a href="<?= base_url("downloads/formato_importacion_productos_compras.csv") ?>">click aqu&iacute; para descargar Plantilla</a></b></label><br>
            <button type="button" class="btn btn-success" onclick="$('#importacion').css('display','block');$('#modo').val('M');">Importar Items</button>
        </div>

    </div>

    <!------------------- I M P O R T A C I O N --------------------->
    <section id="importacion" style="display:none;border-style: solid; border-color: gary; border-width: 1px;">
        <div class="row" style="margin:10px;">
            <div class="col-xs-6 col-sm-4 text-left">
                <input type="file" name="fichero1" class="form-control">
            </div>
        </div>
        <div class="row" style="margin:10px;">
            <div class="col-xs-6 col-sm-4">
                <select name="opciones_csv" id="opciones_csv" class="form-control">
                    <option value="">--Elija--</option>
                    <option value="1">CSV (separador puntoycoma y datos entrecomillados)</option>
                    <option value="2">CSV (solo con separador puntoycoma)</option>
                </select>
            </div>
        </div>
        <div class="row" style="margin:10px;">    
            <div class="col-xs-6 col-sm-4 text-left">
                <button type="submit" class="btn btn-primary">Subir</button>
            </div>
        </div>
    </section>
    
    <div class="row filitas">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 filitas" id="productos">
        </div>
    </div>

    <div class="row filitas">
        <div class="col-xs-12 col-sm-3 col-lg-2">
            <label>Redondeo</label>
            <input type="text" name="redondeo" id="redondeo" value="<?= $redondeo ?>" class="form-control">
        </div>
    </div>

    <div class="row filitas">
        <input type="hidden" name="id_compras" id="id_compras" value="<?= $id_compras ?>">
        <input type="hidden" name="txt_gSubtotal" id="txt_gSubtotal">
        <input type="hidden" name="txt_gIgv" id="txt_gIgv">
        <input type="hidden" name="txt_gTotal" id="txt_gTotal">
        <input type="hidden" name="modo_edicion" id="modo_edicion" value="<?= $modo_edicion ?>">
        <input type="hidden" name="tipogasto" id="tipogasto" value="<?= (isset($tipogasto) ? $tipogasto : 'caja') ?>"> 
    </div>

    <div class="row filitas">
        <div class="col-6 col-sm-4 col-md-2">
            <select name="tipo_pago" id="tipo_pago" class="form-control" required>
                <option value="">----Elija Tipo Pago----</option>
                <option value="1">Caja</option>
                <option value="2">Bancos</option>
            </select>
        </div>
    </div>

    <div class="row filitas">
        <div class="col-md-11">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Guardar Compra</button>
                <button type="button" id="reset" class="btn btn-danger"><?= lang('reset'); ?></button>
                <a href="<?= base_url('compras') ?>" class="btn btn-warning">Regresar</a>
                <!--<button type="button" onclick="rellenar()">rellenar</button>
                <button type="button" onclick="agregar_importado('<?= $_SESSION["usuario"] ?>')">Agregar items importados</button>-->
                <input type="hidden" name="modo" id="modo" value="I">
            </div>
        </div>

        <div class="col-md-1">
            <?php 
                form_submit('add_purchase', '.', 'class="" id="add_purchase"'); 
            ?>
        </div>
        <div class="col-md-1">

        </div>
        
    </div>
      
    <!--<button onclick="verificarUnicidad()">verificarUnicidad</button>-->
    <?php echo form_close();?>

    <form action="" method="post" autocomplete="off">
    </form>

</section>
<script type="text/javascript">
    var gIgv        = 18;
    var lBuscar     = true;

    document.getElementById("campo").addEventListener("keyup", getCodigos)

    function rellenar(){
        $("#date").val("2022-09-28T10:15")
        $("#date_ingreso").val("2022-09-28T09:16")
        $("#tipoDoc").val("2")
        $("#nroDoc").val("10526578")
        $("#proveedor_id").val("7")
        document.getElementById("importacion").style.display = 'block'
        $("#opciones_csv").val("2")
    }

    function getCodigos() {

        console.log("getCodigos....")
        if(lBuscar == true){

            let inputCP = document.getElementById("campo").value
            let lista = document.getElementById("lista")

            if (inputCP.length > 0) {

                /*
                let url = '<?= base_url("sales/buscar2") ?>'
                let formData = new FormData()
                formData.append("campo", inputCP)

                fetch(url, {
                    method: "POST",
                    body: formData,
                    mode: "cors" //Default cors, no-cors, same-origin
                }).then(response => response.json())
                    .then(data => {
                        lista.style.display = 'block'
                        lista.innerHTML = data
                    })
                    .catch(err => console.log(err))
                */

                $.ajax({
                    data : {campo : inputCP},
                    url  : '<?= base_url("sales/buscar2") ?>',
                    type : 'post',
                    success : function(res){
                        //let obji = JSON.parse(res)
                        lista.style.display = 'block'
                        lista.innerHTML = res
                    }
                })
            } else {
                lista.style.display = 'none'
            }
        }
    }

    function mostrar(id,cp) {
        lista.style.display = 'none'
        $("#product_id").val(id)
        lBuscar = false
        $("#campo").val(cp)
        $("#campo").attr("readonly","readonly");
    }

    function validar_gral(){
        console.log("Inicia validar_gral")
        if(ar_items.length == 0){
            if(document.getElementById("importacion").style.display != 'block'){
                alert("Debe ingresar al menos un item")
                return false
            }
        }

        var fecha           = $("#date").val()
        var fecha_ingreso   = $("#date_ingreso").val()


        if(empty(fecha) || fecha=="" || fecha.length == 0 || fecha == 'undefined'){
            mensaje("Debe ingresar la fecha" + fecha)
            return false
        }

        if(empty(fecha_ingreso) || fecha_ingreso=="" || fecha_ingreso.length == 0 || fecha_ingreso == 'undefined'){
            mensaje("Debe ingresar la fecha de ingreso" + fecha_ingreso)
            return false
        }
        console.log("Finaliza validar_gral")

        return true
    }

    function guardar_compra(){
        //console.log("inicia guardar_compra")
        if(validar_gral()){
            //mensaje("Si pasa la validacion");
            return true;
        }else{
            mensaje("No pasa la validación...")
            return false;
        }
    }

    function mensaje(msg){
        alert(msg);
    }

    function agregar(){
        // previa validacion:
        if ($("#quantity").val() <= 0){
            alert("Cantidad no puede ser 0 negativo")
            return false
        }

        if ($("#cost").val() <= 0){
            alert("Costo no puede ser negativo")
            return false
        }

        agregar_item(); // lo ingresa al array ar_items
        
        cargar_items();

        lBuscar = true;

        // Borrando valores casillas
        //document.getElementById("campo").readOnly = false;
        $("#campo").removeAttr("readonly");
        $("#campo").val("")
        $("#quantity").val(0)
        $("#cost").val(0)
        $("#product_id").val("")
        $("#campo").focus()
    }

    function agregar_item(){
        let x1      = document.getElementById("product_id").value
        let combo   = document.getElementById("product_id");
        let x1_name = $("#campo").val() //combo.options[combo.selectedIndex].text;
        let x2      = document.getElementById("quantity").value
        let x3      = document.getElementById("cost").value
        var x5      = parseFloat(x3)

        if(x1.length == 0){
            alert("No ha escogido el producto")
            return 
        }

        if(parseFloat(x2)==0){
            alert("La cantidad no puede ser 0")
            return
        }

        if(parseFloat(x3)==0){
            alert("El costo no puede ser 0")
            return
        }
        if(document.getElementById("tipoDoc").value != 'G'){
            if(document.getElementById("chk_igv").checked == true){
                x3 = x3 / (1+(gIgv/100))
                x3 = x3.toFixed(4)
            }

            var x4      = 1 * x2 * x3
            x4          = x4.toFixed(2)
        }else{
            // el igv es como si fuera 0%
            x3      = x3 * 1
            x3      = x3.toFixed(4)
            var x4  = 1 * x2 * x3
            x4      = x4.toFixed(2)
        }

        ar_items.push({id:x1, 
            name:       x1_name, 
            quantity:   x2, 
            cost:       x3, 
            subtotal:   x4,
            precio:     x5
        })
        console.log("Se agrega Item...")
        console.log(ar_items)
    }

    function cargar_items(){
        
        var Limite = ar_items.length
        var gsubTotal = 0
        var cad = "<table>"

        //cad += "<div class='table-responsive'>"
        cad += '<table id="clasico" class="table table-striped table-bordered">'
        cad += '<thead>'
        cad += '    <tr class="active">'
        cad += '        <th class="col-xs-5 col-sm-4"><?= lang("product"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-1"><?= lang("quantity"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-2">P.U sin Igv</th>'
        //cad += '        <th class="col-xs-2 col-sm-1">Peso_caja</th>'
        cad += '        <th class="col-xs-2 col-sm-2" style="color:red;font-style:italic;">P.U con Igv</th>'
        cad += '        <th class="col-xs-3 col-sm-2" style="text-align:right"><?= lang("subtotal"); ?></th>'
        cad += '        <th class="col-xs-2 col-sm-1" style="">Accion</th>'
        
        cad += '    </tr>'
        cad += '</thead>'
        cad += '<tbody>'
        
        //console.log("funcion cargar_items : ar_items:"+JSON.stringify(ar_items))

        for(let i=0; i<Limite; i++){
            cad += "<tr>"
            
            // Nombre
            cad += '<td style="text-align: left" class="col-xs-5 col-sm-4">' + ar_items[i]["name"] 
            cad += '<input type="hidden" name="product_id[]" value="'+ar_items[i]['id'] + '" class="form-control">'
            cad += '<input type="hidden" name="rubro_id[]" value="' + ar_items[i]['rubro_id'] + '">'
            cad += '<input type="hidden" name="descripo[]" value="' + ar_items[i]['name'] + '"</td>'
            
            // Quantity
            cad += '<td class="col-xs-2 col-sm-1"><input size="4" style="text-align: right;padding:2px;" type="text" name="quantity[]" value="' + ar_items[i]["quantity"] + '"  class="form-control" readonly></td>'
            
            // Costo
            cad += '<td class="col-xs-2 col-sm-2"><input size="9" style="text-align: right" type="text" name="cost[]" value="' + ar_items[i]["cost"] + '"  class="form-control" readonly></td>'
            
            // con Igv
            cad += '<td class="col-xs-2 col-sm-2" style="color:red"><input size="9" style="text-align: right" type="text" name="precio[]" value="' + ar_items[i]["precio"] + '"  class="form-control" readonly>' + '</td>'

            // Subtotal
            let nSubTotalx = ar_items[i]["subtotal"] * 1
            // .toLocaleString('es-PE',{ style: 'currency', currency: 'PEN' })
            cad += '<td style="text-align: right" class="col-xs-3 col-sm-2">' + nSubTotalx.toLocaleString('es-PE') + "</td>"
            
            // Trash
            cad += '<td style="text-align: center" class="col-xs-2 col-sm-1"><a href="#" onclick="quitar_item(ar_items,'+i+')"><i class="fa fa-trash-o"></i></a></td>'
            
            cad += "</tr>"
            gParcial = 1 * ar_items[i]["quantity"] * ar_items[i]["cost"]
            
            gsubTotal += gParcial

            //console.log("vamos ....[i]:" + i +  ", quantity:" + ar_items[i]["quantity"] + ", cost:" + ar_items[i]["cost"] + ", gsubTotal:" + gsubTotal)
        }
        
        // Tema de descuentos .......
        let nDscto = cDscto = 0
        if($("#descuentos").length > 0){
            nDscto = $("#descuentos").val() * 1
            nDscto = nDscto.toFixed(2)
            cDscto = "Dscto. = " + nDscto
        }

        cad += '</tbody>'

        cad += '<tfoot>'

        // *** FILA SUBTOTAL **********
        cad += '    <tr class="active">'
        cad += '        <th class="col-xs-5 col-sm-4"></th>'
        cad += '        <th class="col-xs-2 col-sm-1"></th>'
        cad += '        <th class="col-xs-2 col-sm-2"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
        cad += '        <th class="col-xs-2 col-sm-2 celdas_totales text-right"><?= lang('subtotal'); ?></th>'
        cad += '        <th class="col-xs-3 col-sm-2 celdas_totales text-right"><span id="gsubtotal">0.00</span></th>'
        cad += '        <th class="col-xs-2 col-sm-1 celdas_totales"></th>'
        cad += '    </tr>'

        // *** FILA IGV **********
        cad += '    <tr class="active">'
        cad += '        <th class="col-xs-5 col-sm-4"></th>'
        cad += '        <th class="col-xs-2 col-sm-1"></th>'
        cad += '        <th class="col-xs-2 col-sm-2"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
        cad += '        <th class="col-xs-2 col-sm-2 celdas_totales text-right"><?= lang('igv'); ?></th>'
        cad += '        <th class="col-xs-3 col-sm-2 celdas_totales text-right"><span id="gIgv">0.00</span></th>'
        cad += '        <th class="col-xs-2 col-sm-1 celdas_totales"></th>'
        cad += '    </tr>'

        
        // *** FILA DSCTO **********
        if(nDscto > 0){
            cad += '    <tr class="active">'
            cad += '        <th class="col-xs-5 col-sm-4"></th>'
            cad += '        <th class="col-xs-2 col-sm-1"></th>'
            cad += '        <th class="col-xs-2 col-sm-2"></th>'
            //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
            cad += '        <th class="col-xs-2 col-sm-2 celdas_totales text-right">Dscto.</th>'
            cad += '        <th class="col-xs-3 col-sm-2 celdas_totales text-right">-'+nDscto+'</th>'
            cad += '        <th class="col-xs-2 col-sm-1 celdas_totales"></th>'
            cad += '    </tr>'
        }

        // *** FILA TOTAL **********
        cad += '    <tr class="active">'
        cad += '        <th class="col-xs-5 col-sm-4"></th>'
        cad += '        <th class="col-xs-2 col-sm-1"></th>'
        cad += '        <th class="col-xs-2 col-sm-2"></th>'
        //cad += '        <th style="margin:0px;padding:3px;height:17px;background-color:rgb(185,185,185)" class="col-xs-2 col-sm-1"></th>'
        cad += '        <th class="col-xs-2 col-sm-2 celdas_totales text-right"><?= lang('total_1'); ?></th>'
        cad += '        <th class="col-xs-3 col-sm-2 celdas_totales text-right"><span id="gTotal">0.00</span></th>'
        cad += '        <th class="col-xs-2 col-sm-1 celdas_totales"></th>'
        cad += '    </tr>'
        cad += '</tfoot>'
        cad += "</table>"
        
        document.getElementById("productos").innerHTML = cad

        var nIgv = 0
        var gTotal = 0

        var cSubTotal   = gsubTotal.toFixed(2) * 1;
        cSubTotal       = cSubTotal.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})  // ,{ style: 'currency', currency: 'PEN' }

        var redondeo    = document.getElementById("redondeo").value * 1

        if(document.getElementById("tipoDoc").value != 'G'){
            nIgv    = gsubTotal * (gIgv/100)

            var cIgv        = nIgv.toFixed(2) * 1;
            cIgv            = cIgv.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})

            gTotal          = (gsubTotal.toFixed(2) * 1) + (nIgv.toFixed(2) * 1) - (nDscto * 1) + redondeo
            var cTotal      = gTotal.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})

        }else{
            var cSubTotal       = 0.00
            var cIgv            = 0.00
            var nTotal_2        = (gsubTotal * 1) + redondeo
            var cTotal          = Math.round(nTotal_2*100)/100
            gTotal              = gsubTotal 
            gsubTotal           = 0.00
        }
        
        document.getElementById("gsubtotal").innerHTML      = cSubTotal
        document.getElementById("gIgv").innerHTML           = cIgv
        document.getElementById("gTotal").innerHTML         = cTotal
        
        $("#txt_gSubtotal").val(gsubTotal.toFixed(2))
        $("#txt_gIgv").val(nIgv.toFixed(2))
        $("#txt_gTotal").val(gTotal.toFixed(2)) // Number.parseFloat
        
    }

    function busqueda_precio(){
        console.log("buscando...")
    }

    function quitar_item(aro,pid){
        quitar_elemento(aro,pid)
        cargar_items()
    }

    function quitar_elemento(aro,pid){  // function OK
        aro.splice(pid,1)
    }

    function agregar_importado(usuario){

        $.ajax({
            data    : {usuario : usuario},
            type    : 'get',
            url     : '<?= base_url('Compras/get_tempo') ?>',
            success : function(res){
                
                //console.log(res)
                var obj = JSON.parse(res)

                for(registro in obj){
                    let x1      = obj[registro]["product_id"]
                    let x1_name = obj[registro]["nombre"]
                    let x2      = obj[registro]["cantidad"]
                    let x3      = obj[registro]["precio"]
                    var x5      = parseFloat(x3)

                    if(x1.length == 0){
                        alert("No ha escogido el producto")
                        return 
                    }

                    if(parseFloat(x2)==0){
                        alert("La cantidad no puede ser 0")
                        return
                    }

                    if(parseFloat(x3)==0){
                        alert("El costo no puede ser 0")
                        return
                    }

                    if(document.getElementById("tipoDoc").value != 'G'){
                        if(document.getElementById("chk_igv").checked == true){
                            x3 = x3 / (1+(gIgv/100))
                            x3 = x3.toFixed(4)
                        }

                        var x4      = 1 * x2 * x3
                        x4          = x4.toFixed(2)
                    }else{
                        // el igv es como si fuera 0%
                        x3      = x3 * 1
                        x3      = x3.toFixed(4)
                        var x4  = 1 * x2 * x3
                        x4      = x4.toFixed(2)
                    }

                    ar_items.push({id:x1, 
                        name:       x1_name, 
                        quantity:   x2, 
                        cost:       x3, 
                        subtotal:   x4,
                        precio:     x5
                    })
                    
                    //console.log("Se agrega Item...")
                    //console.log(ar_items)
                }

                cargar_items();

                lBuscar = true;

                // Borrando valores casillas
                $("#campo").val("")
                $("#quantity").val(0)
                $("#cost").val(0)
                $("#product_id").val("")
            }
        })
    }

    <?php
        if($modo == 'M'){
            echo "agregar_importado('" . $_SESSION["usuario"] . "');";
        }
    ?>

</script>