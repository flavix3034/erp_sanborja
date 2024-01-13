<?php
	$fecha = date("Y-m-d") . "T" . date("H:i");
	//$obs = "";
?>
<!--<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css'>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
<style type="text/css">
	.ventas{
		border-style:none; border-width: 1px; border-color:rgb(120,120,120);
	}
	.filitas{
		margin-top: 15px;
		border-style:none; border-width: 2px; border-color:orange;
	}

	.cubo{
		background-color: white;
		border-style:solid; border-width: 1px; border-color:rgb(170,170,170);
		height: 150px;
		width:  150px;
		float:  left;
		margin: 5px;
	}
	.marco-producto{
		margin:2px 0px; 
		height:160px;
	}
	.celdas_totales{
		background-color: rgb(130,170,200); /*rgb(140,160,180);*/
		height: 25px!important;
	}
	.table th{
		height: 35px;
		padding: 4px !important;
	}
	.dropdown {
	  display: inline-block;
	  position: relative;
	}
	.dropdown-content {
	  display: none;
	  position: absolute;
	  width: 100%;
	  overflow: auto;
	  box-shadow: 0px 10px 10px 0px rgba(0,0,0,0.3);
	  background-color:rgb(220,220,220);
	}
	.dropdown:hover .dropdown-content {
	  display: block;
	}
	.dropdown-content a {
	  display: block;
	  color: #000000;
	  padding: 5px;
	  text-decoration: none;
	}
	.dropdown-content a:hover {
	  color: #FFFFFF;
	  background-color: #00A4BD;
	}	

	::selection{
	  color: #fff;
	  background: #664AFF;
	}

	.wrapper{
	  max-width: 450px;
	  margin: 10px auto;
	}

	.wrapper .search-input{
	  background: #fff;
	  width: 100%;
	  border-radius: 5px;
	  position: relative;
	  box-shadow: 0px 1px 5px 3px rgba(0,0,0,0.12);
	}

	.search-input input{
	}

	.search-input.active input{
	}

	.search-input .autocom-box{
	  padding: 0;
	  opacity: 0;
	  pointer-events: none;
	  max-height: 280px;
	  overflow-y: auto;
	}

	.search-input.active .autocom-box{
	  padding: 10px 8px;
	  opacity: 1;
	  pointer-events: auto;
	}

	.autocom-box li{
	  list-style: none;
	  /*padding: 8px 12px;*/
	  display: none;
	  width: 100%;
	  cursor: default;
	  border-radius: 3px;
	}

	.search-input.active .autocom-box li{
	  display: block;
	}
	.autocom-box li:hover{
	  background: #efefef;
	}

	.search-input .icon{
	  position: absolute;
	  right: 0px;
	  top: -10px;
	  height: 55px;
	  width: 55px;
	  text-align: center;
	  line-height: 55px;
	  font-size: 20px;
	  color: #644bff;
	  cursor: pointer;
	}

	.ubica-drop{
		width:140px;
		left:-60px;
	}

	.ver{
		border-style: solid;
		border-color: red;
		border-width:1px;
	}
</style>
<script type="text/javascript">
	function activar_zona_cliente(){
		document.getElementById('zona_cliente').style.display='block'
		var dni_cliente = document.getElementById('dni_cliente').value
		var tipoDoc 	= document.getElementById('tipoDoc').value
		if(dni_cliente == ''){
			if(tipoDoc == '2' || tipoDoc == '5'){ // Boleta o Tk
				document.getElementById('dni_cliente').value = '00000000'
				document.getElementById('btn_buscar').click()
			}
		}
	}

	document.addEventListener("DOMContentLoaded", function(){
    const milisegundos = 5 * 60 * 1000;
	setInterval(
		function(){
    		// No esperamos la respuesta de la petición porque no nos importa
        	fetch("<?= base_url("refrescar.php") ?>");
        },milisegundos);
    });
</script>
<?php 
if(isset($existe_apertura)){
	if($existe_apertura){
?>
	<div class="row">
		<div class="col-12 col-sm-10 col-lg-10 ventas">
			<form name="form1" id="form1" action="<?= base_url("sales/save") ?>" method="POST">
				<div class="row filitas">
					<div class="col-5 col-sm-4 col-lg-4 ventas">
						<label>Fecha</label>
						<input type="datetime-local" name="fecha" id="fecha" value="<?= $fecha ?>" class="form-control">
					</div>
					<div class="col-5 col-sm-3 col-lg-2 ventas">
						<label>Nro. Recibo</label>
						<input type="text" name="txt_recibo" class="form-control tip" id="txt_recibo" readonly>
					</div>
					
				</div>

				<div class="row filitas">
					<div class="col-sm-4 col-lg-4 ventas">
						<label>Tipo Doc</label>
						<?php
							$result = $this->db->where("activo","1")->get("tec_tipos_doc")->result();
							$ar = array();
							$ar[] = "";
							foreach($result as $r){
								$ar[$r->id] = $r->descrip;
							}
							echo form_dropdown('tipoDoc',$ar,'','class="form-control tip" id="tipoDoc" required="required" onchange="correlativo(this);activar_zona_cliente()"');
						?>
					</div>
				</div>

				<div id="zona_cliente" style="display:none">
					<div class="row filitas">
						<div class="col-sm-2 col-lg-3 ventas">
							<label>Dni/Ruc:</label>
							<input type="text" name="dni_cliente" id="dni_cliente" class="form-control" onblur="console.log('Aspecto Busqueda.');">
						</div>
						<div class="col-sm-4 col-lg-3 ventas" style="margin-top: 23px">
							<button id="btn_buscar" type="button" onclick="busqueda_nombre(document.getElementById('dni_cliente'))" class="btn btn-primary">Buscar</button>
						</div>
					</div>
					<div class="row">
						<div class="col-7 col-sm-6 col-lg-4 ventas">
							<label>Nombres:</label>
							<input type="text" name="name_cliente" class="form-control" id="name_cliente" readonly>
							<input type="hidden" name="txt_customer_id" id="txt_customer_id">
						</div>

						<div class="col-6 col-sm-5 col-lg-3 ventas">
							<label>Ruc</label>
							<input type="text" name="txt_cf2" id="txt_cf2" class="form-control" readonly>
						</div>
					</div>
				</div>
				<style type="text/css">
					.producto_st{
						font-family: courier;
						font-weight: bold;
						font-size: 14px;
					}
					.con-borde{
						border-style: solid;
						border-width: 1px;
						border-color: red;
					}
				</style>
				
				<div class="row filitas">

				    <div class="col-8 col-sm-7 col-md-6" style="border-style:none; border-color:blue;">
				    	<label id="lbl_busqueda">Codigo de Barras  </label><br>
				    	<div class="search-input">
				        	<a href="" target="_blank" hidden></a>
				        	<input type="text" class="form-control" name="hdn_descrip" id="hdn_descrip" placeholder="Type to search.." onblur="ejecutar_libre()">

				        	<div class="autocom-box">
				          		
				        	</div>
				        	
				      	</div>
				      	<input type="hidden" name="product_id" id="product_id">
				      	<input type="hidden" name="category_id" id="category_id">
				      	<input type="hidden" name="impuesto" id="impuesto">
				    </div>
				    
					<div class="col-3 col-sm-2" style="border-style:none; border-color:blue;">
						<label>&nbsp;</label><br>
						<div class="dropdown" style="z-index:100;">
							<label id="lbl_busqueda"><button type="button">...</button></label><br>
							<div class="dropdown-content ubica-drop">
								<a href="#" onclick="$('#hdn_codigo').val('CODIGO');$('#lbl_busqueda').html('Codigo de Barra');">Codigo de Barra</a>
								<a href="#" onclick="$('#hdn_codigo').val('PRODUCTO');$('#lbl_busqueda').html('Producto');">Producto</a>
							</div>
						</div>
						<input type="hidden" name="hdn_codigo" id="hdn_codigo" value="CODIGO">
					</div>

				</div>

				<div class="row filitas">	
					<div class="col-4 col-sm-3 col-lg-2">
						<div class="form-group">
							<label>Precio</label>
							<input type="text" name="cost" id="cost" class="form-control">
						</div>
					</div>
					<div class="col-4 col-sm-3 col-lg-2">
						<div class="form-group">
							<label>Cantidad</label>
							<input type="text" name="quantity" id="quantity" class="form-control">
						</div>
					</div>
					<div class="col-6 col-sm-2 col-md-2 col-lg-1">
						<!--<div class="row" style="margin-top:20px">-->
							<!--con IGV: <input type="checkbox" id="chk_igv" name="chk_igv" value="1">-->
							<br><button type="button" id="boton_mas" class="btn btn-success" onclick="Agregar()" style="font-size: 18px; font.font-weight: bold;">+</button>
							<!--<button type="button" onclick="llenar()">llenar</button>-->
						<!--</div>-->
					</div>
					<div class="col-6 col-sm-12 col-md-3 col-lg-3 ventas text-left">
						<label>Precio x Mayor</label><br>
						<input type="checkbox" name="tipo_precio" id="tipo_precio" value="1" onchange="cambiar_tipo_precios()"><br>
					</div>
				</div>
				<input type="hidden" name="subtotal" id="subtotal">
				<input type="hidden" name="igv" id="igv">
				<input type="hidden" name="total" id="total">
				<!--<input type="text" name="items" id="items">-->

				<!-- ESTE ES EL DIV DE LOS PRODUCTOS YA SELECCIONADOS --->
				<div class="row filitas">
					<div class="col-sm-12 col-md-11 col-lg-10" style="border-style:none; border-color:blue; border-width:1px">
						
						<div class="row">
							<div class="col-sm-12 col-md-12" id="taxi" style="border-style:none; border-color:red; border-width:1px">
							</div>
						</div>
					</div>
				</div>

				<!-------  F O R M A   D E   P A G O S ---------------------------------> 
				<div class="row filitas">
				
					<div class="col-5 col-sm-2 col-lg-2 ventas">
						<label>Monto</label>
						<input type="text" name="forma_pago_monto" id="forma_pago_monto" class="form-control">
					</div>
				
					<div class="col-6 col-sm-4 col-lg-3 ventas">
						<label>Forma de Pago 1:</label>
						<?php
							$result = $this->db->where("activo","1")->get("tec_forma_pagos")->result();
							$ar = array();
							$ar[] = "";
							foreach($result as $r){
								$ar[$r->forma_pago] = $r->descrip;
							}
							echo form_dropdown('forma_pago',$ar,'','class="form-control tip" id="forma_pago" required="required"');
						?>
					</div>
				
					<div class="col-1 col-sm-4 col-lg-3 ventas">
						<label>&nbsp;</label><br>
						<a href="#" onclick="document.getElementById('div-forma_pago2').style.display='block';">
							<span class="glyphicon glyphicon-plus-sign iconos" style="font-size:22px;color:rgb(100,180,255);"></span>
						</a>
					</div>
				</div>
				<div class="row filitas" id="div-forma_pago2" style="display:none">
					<div class="col-5 col-sm-3 col-lg-2 ventas">
						<label>Monto 2</label>
						<input type="text" name="forma_pago_monto2" id="forma_pago_monto2" class="form-control">
					</div>
					<div class="col-6 col-sm-3 col-lg-3 ventas">
						<label>Forma de Pago 2:</label>
						<?php
							$result = $this->db->where("activo","1")->get("tec_forma_pagos")->result();
							$ar = array();
							$ar[] = "";
							foreach($result as $r){
								$ar[$r->forma_pago] = $r->descrip;
							}
							echo form_dropdown('forma_pago2',$ar,'','class="form-control tip" id="forma_pago2" required="required"');
						?>
					</div>
					<div class="col-1 col-sm-6 col-lg-6 ventas">
						&nbsp;
					</div>
				</div>
				
				<div class="row filitas" style="border-style:none; border-color:black;">
					<div class="col-12 col-sm-12 ventas">
						<button type="button" class="btn btn-primary" onclick="grabar_venta()">Guardar</button>
					</div>
				</div>

			<?= form_close() ?>
		</div>
		<div class="col-sm-6 col-lg-6 ventas d-none" id="grid_productos">

		<?php for($nRow=1; $nRow<=4; $nRow++){ ?>
			<div class="row">
				<div class="col-sm-3 col-md-3 marco-producto" id="r<?= $nRow ?>-1">
					<button id="r<?= $nRow ?>-1-btn">
						<div style="height:90px; width:90px; padding: 3px; margin:6px;" id="r<?= $nRow ?>-1-img"></div>
					</button><br>
					<div class="mariposa" id="r<?= $nRow ?>-1-label"></div>
				</div>
				<div class="col-sm-3 col-md-3 marco-producto" id="r<?= $nRow ?>-2">
					<button id="r<?= $nRow ?>-2-btn">
						<div style="height:90px; width:90px; padding: 3px; margin:6px;" id="r<?= $nRow ?>-2-img"></div>
					</button><br>
					<div class="mariposa" id="r<?= $nRow ?>-2-label"></div>
				</div>
				<div class="col-sm-3 col-md-3 marco-producto" id="r<?= $nRow ?>-3">
					<button id="r<?= $nRow ?>-3-btn">
						<div style="height:90px; width:90px; padding: 3px; margin:6px;" id="r<?= $nRow ?>-3-img"></div>
					</button><br>
					<div class="mariposa" id="r<?= $nRow ?>-3-label"></div>
				</div>
				<!--<div class="col-sm-3 col-md-3 marco-producto" id="r<?= $nRow ?>-4">
					<button id="r<?= $nRow ?>-4-btn">
						<div style="height:90px; width:90px; padding: 3px; margin:6px;" id="r<?= $nRow ?>-4-img"></div>
					</button><br>
					<div class="mariposa" id="r<?= $nRow ?>-4-label"></div>
				</div>-->
			</div>
		<?php } ?>
			
		</div>
	</div>
<?php
	}else{
		echo "<div class=\"alert alert-danger\" style=\"margin-top:20px;\">INGRESE APERTURA DE CAJA</div>";
	}
}else{
	echo "INGRESE APERTURA DE CAJA";
}
?>
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

<!-- </section> -->

<script type="text/javascript">

	var gIgv = 18
	function expand(obj) { obj.size = 5; } 
	function unexpand(obj) { obj.size = 1; }

	function ejecutar_libre(){
        if(document.getElementById('hdn_codigo').value == 'LIBRE'){
	        let texto = $("#hdn_descrip").val()
	        $('#product_id').val(99999)
	        $('#quantity').val(1)
	        $('#cost').focus()
	        $('#impuesto').val(gIgv)

    	}
	}

	function ejecutar_codigo_barra(){
		$.ajax({
			data 	: {code : $("#hdn_descrip").val()},
			type 	: "POST",
			url 	: "<?= base_url("sales/buscar_codigo") ?>",
            error: function(){
                alert("error petición ajax");
            },
            success: function(data){                                                    
                var obj = JSON.parse(data)
                
                for(registro in obj){
					$("#product_id").val(obj[registro]["id"])
					$("#hdn_descrip").val(obj[registro]["name"] + " [" + obj[registro]["stock"] + "]")
					$("#impuesto").val(obj[registro]["impuesto"])
                }
                
                busqueda_precio()

            }
		})
	}

	$(document).ready(function(){
	    var consulta;
	                                                                      
	     //hacemos focus al campo de búsqueda
	    $("#code").focus();

	    /*$("#product_id").keyup(function(e){
	    	if(e.key == 'Enter'){
	    		this.prevent_default()
	    		$('#product_id').change()
	    	}
	    })*/

	});    

	$(document).ready(
		function(){
	        $("#myBtn").click(function(){
	            $("#pizarra").modal();
        	});
		
	        $("#myBtn2").click(function(){
	            $("#pizarra2").modal();
	            var dni_cliente = document.getElementById("dni_cliente").value
	            if(dni_cliente.length != 11){
	            	$("#cf1").val($("#dni_cliente").val())
	            }else{
	            	$("#cf2").val($("#dni_cliente").val())
	            }
        	});

		}
	)

	ar_items = new Array()

	function cambiar_tipo_precios(){
		var prod1 = document.getElementById("product_id").value
		var opcion1 = document.getElementById("tipo_precio").checked ? '1' : '0'
		//alert("Es opcion1: " + opcion1)
		$.ajax({
			data 	:{product_id : prod1, opcion: opcion1},
			url 	:'<?= base_url("sales/obtener_tipo_precios") ?>',
			type 	:'get',
			success :function(res){
				//alert(res)
				document.getElementById("cost").value = res
			}
		})
	}

	function busqueda_nombre(obj){
		var tipoDoc 		= document.getElementById("tipoDoc").value
		var dni_cliente 	= document.getElementById("dni_cliente").value
		if(tipoDoc == '1'){ // factura
			if(dni_cliente.length != 11){
				alert("Debe ingresar un Ruc con 11 digitos. Tiene "+dni_cliente.length)
				return false
			}
		}
		var datin = obj.value
		if(datin.length > 0){
			$.ajax({
				data 	: {dato1: datin},
				url 	: '<?= base_url("clientes/busqueda_nombre") ?>',
				type 	: "get",
				success : function(response){
					console.log(response)
					var obj = JSON.parse(response)
					if(obj.rpta){
						document.getElementById("name_cliente").value 	= obj.name_cliente
						document.getElementById("txt_cf2").value 		= obj.cf2
						document.getElementById("txt_customer_id").value = obj.id
					}else{
						//document.getElementById("myBtn2").click()
			            $("#pizarra2").modal();
			            var dni_cliente = document.getElementById("dni_cliente").value
			            if(dni_cliente.length != 11){
			            	$("#cf1").val($("#dni_cliente").val())
			            }else{
			            	$("#cf2").val($("#dni_cliente").val())
			            }

					}
				}
			})
		}
	}

	function busqueda_precio(){
		var datin = document.getElementById("product_id").value
		$.ajax({
			data 	: {dato1: datin, tipo_precio: document.getElementById('tipo_precio').checked == true ? 'por_mayor' : 'por_menor'},
			url 	: '<?= base_url("products/busqueda_precio") ?>',
			type 	: "post",
			success : function(response){
				document.getElementById("cost").value = response
				let	valore = 1
				document.getElementById("quantity").value = valore
				document.getElementById("quantity").focus()
				$('#boton_mas').focus()
			}
		})
	}

	function Agregar(){
        // previa validacion:
        if ($("#quantity").val() <= 0 && $('#category_id').val()!='SERVICIOS' ){  //  15 es SERVICIOS
            alert("Cantidad no puede ser 0 negativo")
            return false
        }

        if ($("#cost").val() <= 0){
            alert("Costo no puede ser negativo")
            return false
        }

        if($("#product_id").val() == ''){
        	alert("Debe seleccionar un producto")
        	return false
        }

        agregar_item(); // lo ingresa al array ar_items
        
        cargar_items(); // lo visualiza en la grilla

        // Borrando valores casillas
        $("#quantity").val(0)
        $("#cost").val(0)
        $("#code").val("")
        //document.getElementById('resultado').style.display = 'none'
        //$("#product_id").empty()
        $("#product_id").val("")
        document.getElementById("hdn_descrip").readOnly = false;
        $('#hdn_descrip').val("")
        //unexpand(document.getElementById('product_id'))
        $("#hdn_descrip").focus()

    }

    function cargar_items(){
        var Limite = ar_items.length
        var gsubTotal 	= 0
        var gTotal 		= 0
        var cad = "<table>"

        cad += "<div class='table-responsive'>"
        cad += '<table id="clasico" class="table table-striped">'
        //cad += '<thead>'
        /*
        cad += '    <tr class="active">'
        cad += '        <th class="col-2 col-sm-2 col-md-2">Producto</th>'
        cad += '		<th class="col-2 col-sm-2 col-md-2">Obs</th>'
        cad += '        <th class="col-2 col-sm-2 col-md-2">Cant.</th>'
        cad += '        <th class="col-2 col-sm-2 col-md-2">Costo U.</th>'
        cad += '        <th class="col-2 col-sm-2 col-md-2" style="text-align:right">Item</th>'
        cad += '        <th class="col-1 col-sm-1 col-md-1" style="width:25px;"><i class="fa fa-trash-o"></i></th>'
        cad += '    </tr>'
        */

        cad += '<tr>'
        cad += '<th>Producto</th>'
        cad += '<th>Obs</th>'
        cad += '<th>Cant.</th>'
        cad += '<th>Costo U.</th>'
        cad += '<th>Item</th>'
        cad += '<th>.</th>'
        cad += '</tr>'
        //cad += '</thead>'
        //cad += '<tbody>'
        
        var ctrl_descrip = ""
        var nIgv_real = 0
		for(let i=0; i<Limite; i++){
            cad 			+= "<tr>"
            
            // Descrip Producto
            ctrl_descrip 	= '<input type="text" name="descripo[]" value="' + ar_items[i]["name"] + '" class="form-control" readonly>'
			
			// hidden id
			cad 			+= '<td style="text-align: left">' + ctrl_descrip + '<input type="hidden" name="item[]" value="'+ar_items[i]['id'] + '" class="form-control"></td>'
            
            // hidden tax (impuesto)
            cad 			+= '<input type="hidden" name="impuestos[]" value="' + ar_items[i]['impuesto'] + '">'

            // Obs
            cad 			+= '<td>' + '<input type="text" class="form-control" name="obs[]" id="obs[]">' + "</td>";

            // Cantidad
            cad 			+= '<td><input size="4" style="text-align: right" type="text" name="quantity[]" value="' + ar_items[i]["quantity"] + '"  class="form-control" readonly></td>'
            
            // Costo
            cad 			+= '<td style=""><input size="9" style="text-align: right;padding: 6.4px 6.4px" type="text" name="cost[]" value="' + ar_items[i]["cost_r"] + '"  class="form-control" readonly></td>'
			
			let nSubTotalx = ar_items[i]["subtotal"] + ""
			let n_sub_total = parseFloat(ar_items[i]["quantity"]) * parseFloat(ar_items[i]["cost_r"])
			n_sub_total = n_sub_total.toFixed(2)
            
			// Subtotal
            cad 			+= '<td style="padding-right:5px;"><input type="hidden" name="sub[]" value="' + nSubTotalx + '" style="text-align: right;" class="form-control" readonly>'
            cad 			+=  '<input type="text" name="sss[]" value="' + n_sub_total + '" style="text-align: right;padding: 6.4px 6.4px;" class="form-control" readonly></td>'
            
            // Trash
            cad 			+= '<td style="text-align: center; padding-top:20px;"><a href="#" onclick="quitar_item('+i+')"><i class="glyphicon glyphicon-trash"></i></a></td>'
            
            cad 			+= "</tr>"
            
            gParcial 		= 1 * ar_items[i]["quantity"] * ar_items[i]["cost"]
            
            gTotal 			+= gParcial

            nIgv_real 		+= gParcial * (ar_items[i]["impuesto"]/100)
        }
        
        // Tema de descuentos .......
        var nDscto = cDscto = 0
        if($("#descuentos").length > 0){
            nDscto = $("#descuentos").val() * 1
            nDscto = nDscto.toFixed(2)
            cDscto = "Dscto. = " + nDscto
        }

        //cad += '</tbody>'

        //cad += '<tfoot>'

        // *** FILA SUBTOTAL **********
        cad += '    <tr class="active">'
        cad += '        <th class="celdas_totales"><?= lang('subtotal'); ?></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales text-right"><span id="gsubtotal">0.00</span></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '    </tr>'

        // *** FILA IGV **********
        cad += '    <tr class="active">'
        cad += '        <th class="celdas_totales"><?= lang('igv'); ?></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales text-right"><span id="gIgv">0.00</span></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '    </tr>'

        
        // *** FILA DSCTO **********
        if(nDscto > 0){
            cad += '    <tr class="active">'
            cad += '        <th style="font-weight:bold" class="celdas_totales">Dscto.</th>'
            cad += '        <th class="celdas_totales"></th>'
            cad += '        <th class="celdas_totales"></th>'
            cad += '        <th class="celdas_totales"></th>'
            cad += '        <th class="celdas_totales text-right">-'+nDscto+'</th>'
            cad += '        <th class="celdas_totales"></th>'
            cad += '    </tr>'
        }

        // *** FILA TOTAL **********
        cad += '    <tr class="active">'
        cad += '        <th class="celdas_totales">Total</th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '        <th class="celdas_totales text-right"><span id="gTotal" style="font-size:16px">0.00</span></th>'
        cad += '        <th class="celdas_totales"></th>'
        cad += '    </tr>'
        //cad += '</tfoot>'
        cad += "</table>"
        
        document.getElementById("taxi").innerHTML = cad

        var nIgv = 0
        gsubTotal = gTotal //  /(1+(gIgv/100))

        var cSubTotal   = gsubTotal.toFixed(2) * 1;
        //cSubTotal       = cSubTotal.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})  // ,{ style: 'currency', currency: 'PEN' }

        if(document.getElementById("tipoDoc").value != 'G'){
            nIgv    = nIgv_real; //gsubTotal * (gIgv/100)

            var cIgv        = nIgv.toFixed(2) * 1;
            //cIgv            = cIgv.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})

            gTotal          = (gsubTotal.toFixed(2) * 1) + (nIgv.toFixed(2) * 1) - (nDscto * 1)
            //var cTotal      = gTotal.toLocaleString('es-PE',{minimumFractionDigits : 2, maximumFractionDigits : 2})
            var cTotal 		= gTotal


        }else{
            var cSubTotal       = 0.00
            var cIgv            = 0.00
            var cTotal          = gsubTotal.toFixed(2)
            //cTotal			= cTotal.parseFloat() * 1.00
            //gTotal            = gsubTotal 
            gsubTotal           = 0.00
        }

        document.getElementById("gsubtotal").innerHTML      = cSubTotal
        document.getElementById("gIgv").innerHTML           = cIgv
        document.getElementById("gTotal").innerHTML         = cTotal.toFixed(2)

        // Para la forma de pago
        document.getElementById("forma_pago_monto").value 	= cTotal.toFixed(2)
        
    }

    function agregar_item(){
        var x1      = document.getElementById("product_id").value
        var combo   = document.getElementById("product_id");
        //var x1_name = combo.options[combo.selectedIndex].text;
        var x1_name = quitar_hasta_letra($('#hdn_descrip').val(),'[')
        var x2      = document.getElementById("quantity").value
        var x3      = document.getElementById("cost").value
        var x3_r 	= document.getElementById("cost").value
        var nImp 	= $('#impuesto').val()

        if(document.getElementById("tipoDoc").value != 'G'){
            //if(document.getElementById("chk_igv").checked == true){
                x3 = x3 / (1+(nImp/100))
                x3 = x3.toFixed(4)
            //}

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
            name 	:x1_name, 
            quantity :x2, 
            cost 	:x3,
            cost_r 	:x3_r, 
            subtotal : x4,
            impuesto : nImp
        })
    }


	function quitar_hasta_letra(inputString, character) {
	    const index = inputString.indexOf(character);
	    if (index !== -1) {
	        const truncatedString = inputString.substring(0, index);
	        return truncatedString;
	    } else {
	        return inputString;
	    }
	}

    function quitar_item(pid){
        ar_items.splice(pid,1);
        cargar_items()
    }
	
	function grabar_venta(){
		if(validar()){
			$("#subtotal").val( $("#gsubtotal").html() )
			$("#igv").val( $("#gIgv").html()) 
			$("#total").val( $("#gTotal").html() ) 
			document.getElementById("form1").submit()
		}
	}
	
	function validar(){
		fecha 			= $("#fecha").val()
		dni_cliente 	= $("#dni_cliente").val()
		name_cliente 	= $("#name_cliente").val() 
		tipoDoc 		= $("#tipoDoc").val()
		forma_pago 		= $("#forma_pago").val()
		txt_recibo 		= $("#txt_recibo").val()
		
		if(empty(fecha)){
			mensaje("Ingrese fecha")
			return false;
		}

		if(empty(name_cliente)){
			mensaje("Ingrese nombre cliente")
			return false;
		}

		if(empty(tipoDoc)){
			mensaje("Ingrese tipo de documento")
			return false;
		}

		if(empty(forma_pago) || forma_pago=='0'){
			mensaje("Ingrese forma de pago")
			return false;
		}
	
		if(tipoDoc == '1' || tipoDoc == '2'){
			if(empty(dni_cliente)){
				mensaje("ingrese dni correcto")
				return false;
			}
		}

		if(ar_items.length == 0){
			mensaje("No ha ingresado algún producto.")
			return false
		}

		if(empty(txt_recibo)){
			mensaje("No ha ingresado el Numero de Documento")
			return false
		}

		if(tipoDoc == '1'){ // Factura
			if(empty( document.getElementById("txt_cf2").value )){
				mensaje("El cliente debe tener registrado su Ruc.")
				return false
			}
		}
		
		// validando los montos de la forma de pago
		$nAcu = document.getElementById("forma_pago_monto").value * 1
		if(document.getElementById("div-forma_pago2").style.display == "block"){
			
			// Validar la forma de pago 2
			forma_pago2 = document.getElementById("forma_pago2").value
			if(empty(forma_pago2) || forma_pago2=='0'){
				mensaje("Ingrese segunda forma de pago")
				return false;
			}

			$nAcu += document.getElementById("forma_pago_monto2").value*1
		}
		if ($nAcu != $("#gTotal").html()*1){
			mensaje("Los monto de la forma de Pago no suman el Total")
			return false
		}

		return true
	}

	function mensaje(cad){
		alert(cad)
	}
	
	function llenar(){
		$("#fecha").val("2021-11-13")
		$("#txt_recibo").val("123")
		$("#dni_cliente").val("07504848")
		$("#name_cliente").val("Edilberto Benites")
		$("#tipoDoc").val("2") // boleta
		$("#forma_pago").val("cash") // cash
	}

	function correlativo(obj){
		$.ajax({
			data 	:{tipo:obj.value},
			type 	:'get',
			url 	:'<?= base_url('sales/correlativo') ?>',
			success : function(res){
				document.getElementById("txt_recibo").value = res
			}
		})
	}

    function ver_documento(id){
        $.ajax({
            url     : '<?= base_url('sales/view/') ?>' + id,
            type    :'get',
            success : function(response){
                $(".modal-body").html(response)
                document.getElementById("myBtn").click()
            }
        })
    }

    function llenar_grilla(categoria){
    	
    	$.ajax({
    		data : {categoria : categoria},
    		type : "post",
    		url  : '<?= base_url('products/mostrar') ?>',
    		success : function(res){
    			var obj = JSON.parse(res)
    			var tablar = ""
    			var x = 0
    			var y = 1
    			var la_funcion = ""
		    	for(registro in obj){
		    		x = x + 1
		    		if(x <= 3 && y <= 4){
						document.getElementById("r"+y+"-"+x+"-img").innerHTML = "<img src=\"../imagenes/" + obj[registro]["imagen"] + "\" style=\"width:90px;height:90px\">";
			    		document.getElementById("r"+y+"-"+x+"-label").innerHTML = obj[registro]["name"] + " " + obj[registro]["marca"] + " " + obj[registro]["modelo"] + " " + obj[registro]["color"]
			    		la_funcion = 'escoger(' + obj[registro]["id"] + ')'
			    		document.getElementById("r"+y+"-"+x+"-btn").setAttribute('onclick',la_funcion)
			    		console.log(la_funcion)
			    	}else{
			    		x = 0
			    		y = y + 1
			    	}
		    	}

		    	//document.getElementById("grid_productos").innerHTML = tablar
    		}
    	})
    	
    }

    function escoger(miId){
    	//alert("Vamos muchanchos!! "+miId)
    	$("#product_id").val(miId)
    	var quantity = $("#quantity").val()
    	if(empty(quantity)){
    		$("#quantity").val(1)	
    	}else{
    		$("#quantity").val(parseFloat($("#quantity").val()) + 1)	
    	}
    	
    	//$("#cost").val() = 9.99
    	busqueda_precio(document.getElementById("product_id"))
    }

    llenar_grilla('')

	let suggestions = [];

	// getting all required elements
	const searchWrapper 	= document.querySelector(".search-input");
	const inputBox 			= searchWrapper.querySelector("input");
	const suggBox 			= searchWrapper.querySelector(".autocom-box");
	const icon 				= searchWrapper.querySelector(".icon");
	let linkTag 			= searchWrapper.querySelector("a");
	let webLink;

	// if user press any key and release
	inputBox.onkeyup = (e)=>{
	    
	    if(document.getElementById('hdn_codigo').value == 'CODIGO'){ // por codigo de barra)	

    		if(e.key == 'Enter'){

    			ejecutar_codigo_barra()

    		}

    	}else if(document.getElementById('hdn_codigo').value == 'PRODUCTO'){
		
		    let userData = e.target.value; //user enetered data
		    
		    $.ajax({
		    	data : {b : userData},
		    	url  : '<?= base_url("sales/buscar") ?>',
		    	type : 'post',
		    	success : function(res){

				    if(res.length > 0){
					    
					    let resx = res.replace('/},/g','},\n')
					    //console.log("RAP:"+resx)

					    let obj = JSON.parse(res)

					    let emptyArray = [];

						for(casco in obj){
					    	let cad_stock = ""
					    	if (obj[casco]['prod_serv'] == 'P'){
					    		cad_stock = " [" + obj[casco]['stock'] + "]"
					    	}
					    	emptyArray.push("<li mio=\"" + obj[casco]['id'] + "\" categoria=\"" + obj[casco]['categoria'] + "\" impuesto=\"" + obj[casco]['impuesto'] + "\">" + obj[casco]['name'] + cad_stock + "</li>")
						}

				        searchWrapper.classList.add("active"); //show autocomplete box

					    // Limpiando antes de agregar
					    $(".autocom-box").empty()

					    showSuggestions(emptyArray)

						let allList = suggBox.querySelectorAll("li");
				        for (let i = 0; i < allList.length; i++){
				            //adding onclick attribute in all li tag
				            allList[i].setAttribute("onclick", "$('#product_id').val(this.getAttribute('mio'));$('#category_id').val(this.getAttribute('categoria'));$('#impuesto').val(this.getAttribute('impuesto'));select(this);document.getElementById('hdn_descrip').readOnly=true;");
				        }
					    
					    if(userData){
					    }else{
					        searchWrapper.classList.remove("active"); //hide autocomplete box
					    }
					    
					}
		    	}
		    })

		
		}else if(document.getElementById('hdn_codigo').value == 'LIBRE'){

			if(e.key == 'Enter'){
				ejecutar_libre()
			}

		}
	}

	function select(element){
	    let selectData = element.textContent;
	    busqueda_precio()
	    inputBox.value = selectData;
	    /*icon.onclick = ()=>{
	        webLink = `https://www.google.com/search?q=${selectData}`;
	        linkTag.setAttribute("href", webLink);
	        linkTag.click();
	    }*/
	    searchWrapper.classList.remove("active");
	}

	function showSuggestions(list){
	    let listData;
	    /*if(!list.length){
	        userValue = inputBox.value;
	        listData = `<li>${userValue}</li>`;
	    }else{*/
	    	listData = list.join('');
	    	
	    //}
	    suggBox.innerHTML = listData;
	}

</script>

	<?php
		$name = "";
		$cf1 = "";
		$cf2 = "";
		$phone = "";
		$email = "";
		$direccion = "";
		$cerrar   = isset($cerrar) ? $cerrar : "";
	?>

	<div class="modal fade" id="pizarra2" role="dialog">
	    <div class="modal-dialog">

			<!-- Modal content-->
	      	<div class="modal-content">
	        	<div class="modal-header">
       				<h4 style="width:600px!important;">Agregar Cliente</h4>		
       		   		<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	</div>
	        	<div class="modal-body">
					<div class="row" style="margin-left:10px;">
					
						<div class="col-sm-4 col-lg-4 ventas">
							<label>Nombre</label>
							<?= form_input('name', $name, 'class="form-control tip" id="name"'); ?>
						</div>

						<div class="col-sm-4 col-lg-3 ventas">
							<label>Dni:</label>
							<?= form_input('cf1', $cf1, 'class="form-control" id="cf1"'); ?>
						</div>

						<div class="col-sm-4 col-lg-3 ventas">
							<label>Ruc:</label>
							<?= form_input('cf2', $cf2, 'class="form-control" id="cf2"'); ?>
						</div>
						
					</div>

					<div class="row filitas" style="margin-left:10px;">
					
						<div class="col-sm-4 col-md-4 ventas">
							<label>Telf/Celular</label>
							<?= form_input('phone', $phone, 'class="form-control" id="phone"'); ?>
						</div>

						<div class="col-sm-4 col-md-5 ventas">
							<label>Email:</label>
							<?= form_input('email', $email, 'class="form-control" id="email"'); ?>
						</div>

						<div class="col-sm-4 col-md-11 ventas">
							<label>Direccion:</label>
							<?= form_input('direccion', $direccion, 'class="form-control" id="direccion"'); ?>
							<input type="hidden" name="cerrar" value="<?= $cerrar ?>">
						</div>
						
					</div>

					<div class="row filitas" style="margin-left:10px;">
						<div class="col-sm-3 col-md-3 ventas">
							<br>
							<button type="button" class="form-control btn btn-primary" onclick="guardar_cliente()">Guardar</button>
						</div>
					</div>

	        	</div>
	        	<div class="modal-footer">
	          		<button id="cerrar_modal2" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        	</div>
	        </div>
		</div>
	</div>


	<script type="text/javascript">
		ar_items = new Array()

		function guardar_cliente(){
			//console.log("validando cliente")
		
			if(empty(document.getElementById("name").value)){
				mensaje("Ingrese Nombre")
				return false;
			}

			var cf1 = document.getElementById("cf1").value
			var cf2 = document.getElementById("cf2").value

			if(empty(cf1) && empty(cf2)){
				mensaje("Falta ingresar datos en dni o Ruc")
				return false;
			}

			// Validando si es factura
			if(document.getElementById("tipoDoc").value == '1'){
				if(empty("cf2")){
					mensaje("Debe ingresar el Ruc, se trata de una Factura.")
					return false;
				}

				if (cf2.length != 11 && cf2.length != 0){
					mensaje("Ruc debe tener 11 caracteres")
					return false;
				}
			}

			grabar_cliente();

			document.getElementById("cerrar_modal2").click()

			
		}

		function mensaje(cad){
			alert(cad)
		}
		
		function llenar(){
		}

		function grabar_cliente(){
			//alert("Estor Grabando el customer")
			$.ajax({
				data: {
					name 	: document.getElementById("name").value,
					cf1 	: document.getElementById("cf1").value,
					cf2 	: document.getElementById("cf2").value,
					phone 	: document.getElementById("phone").value,
					email 	: document.getElementById("email").value,
					direccion:document.getElementById("direccion").value
				},
				url : "<?= base_url("clientes/save") ?>",
				type: "get",
				success: function(response){
					console.log(response)
					//document.getElementById("dni_cliente").focus()
					document.getElementById("btn_buscar").click()
				}
			})
		}
	</script>