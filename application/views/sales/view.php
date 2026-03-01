<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8" />
 <title>View_layout</title>
 <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--<lin--k rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">-->
 
 <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
 <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
 <link href="<?= base_url("assets/plugins/font-awesome/css/font-awesome.css") ?>" rel="stylesheet" type="text/css" />
 
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
 <!--<scrrript src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>
<body>
	<div class="container-fluid">
		<?php
			//a.tipoDoc, concat(a.serie,\'-\',a.correlativo) recibo, a.customer_name, concat(d.cf1,\' \',d.cf2) doc_personal, a.date(date) as fech
			foreach($query->result() as $r){
				$tipo 		= $r->tipo_documento;
				$recibo 	= $r->recibo;
				$c_tipo_descrip = "";
				
				if($tipo == 'Boleta'){ // Boleta : 2
   	        		$tipoDoc_client = "1"; // DNI
   	        		$c_tipo_descrip = 'Boleta de venta eletronica';
   	        		$cDesComprobante = 'de la Boleta electr&oacute;nica';
	         	}elseif($tipo == 'Factura'){ // Factura : 1
	            	$tipoDoc_client = "6"; // RUC
	            	$c_tipo_descrip = 'Factura de venta electronica';
	            	$cDesComprobante = 'de la Factura electr&oacute;nica';
	        	}else{
	         		$tipoDoc_client = "1";
	         		$c_tipo_descrip = $tipo;
	         		$cDesComprobante = 'del comprobante';
	         	}
				$razon 				= $r->razon;
				$doc_personal = $r->doc_personal;
				$fecha 				= $r->fecha;
				$total1 			= number_format($r->total,2);
				$total_discount = number_format((is_null($r->total_discount) ? 0 : $r->total_discount),2);
				$total_tax 		= number_format($r->total_tax,2);
				$grand_total 	= number_format($r->grand_total,2);
			}

			$cSql = "select id, name, code, address1, city as provincia, state as distrito, nombre_empresa, ruc, nota_pie".
				" from tec_stores where id = ?";
			
			$query2 = $this->db->query($cSql,array($_SESSION["store_id"]));
			foreach($query2->result() as $r){
				$nombre_comercial 	= $r->name;
				$nombre_empresa 	= $r->nombre_empresa;
				$direccion 			= $r->address1;
				$ruc 				= $r->ruc;
				$nota_pie 			= $r->nota_pie;
			}

			$ar_datos1 		= explode("-",$recibo);
			$serie 			= $ar_datos1[0];
			$numero 		= $ar_datos1[1];

			$RUC 			= $ruc;
			$TIPO 			= $tipo;
			$SERIE 			= $serie;
			$NUMERO 		= $numero;
			$MTO_IGV 		= $total_tax;
			$MTO_COMPRO 	= $grand_total;
			$FECHA 			= $fecha;
			$TIPO_DOC 		= $tipoDoc_client;
			$NRO_DOC 		= $doc_personal;
			$COD_HASH 		= "";

			$data_qr = "{$RUC}|{$TIPO}|{$SERIE}|{$NUMERO}|{$MTO_IGV}|{$MTO_COMPRO}|{$FECHA}|{$TIPO_DOC}|{$NRO_DOC}|{$COD_HASH}";

			$etiqueta_razon 	= "Razon Social";
			$etiqueta_ruc 		= "RUC";
			if($tipo != "Factura"){
				$etiqueta_razon 	= "Nombre";
				$etiqueta_ruc 		= "Dni";
			}
		?>
		<div class="row" id="seleccion" style="border-style: none; border-width: 2px; border-color:rgb(60,60,60); padding:5px;">
			
			<style type="text/css">
		    	body{
		      		color: rgb(0,50,150);
		      		font-family: Arial;
		    	}
		    	#tabloide{
		    		font-size: 11px;
		    	}
		    	.c-cliente{
		    		font-weight:bold; 
		    		font-size: 12px; 
		    		color:rgb(160,0,0);
		    		text-align: right;
		    		min-width: 50px;
		    	}
		    	.c-datos{
		    		font-weight:bold; 
		    		font-size: 12px;
		    		min-width: 170px;
		    	}
			</style>

			
			<div class="col-sm-6 col-md-5 col-lg-4" style="border-style: none; border-width: 2px; border-color:rgb(60,60,60); padding:3px;"><!-- EXPETO -->

				<div class="row" style="margin:auto">
					<div class="col-xs-12 col-md-12" style="text-align:center">
						<img src="<?= base_url("imagenes/logo1.png") ?>" height="40px">
					</div>
				</div>

				<div class="row" style="margin-bottom:10px; margin:auto">
					<div class="col-sm-12 col-md-12" style="text-align:center">
						<!--<span style="font-size:18px;font-weight: bold;"><?= $nombre_comercial ?></span><br>-->
						<span style="font-size:18px;font-weight: bold;"><?= $nombre_empresa ?></span><br>
						<span style="font-size:14px"><?= $direccion ?></span><br>
						<span style="font-size:14px">RUC: <?= $ruc ?></span><br>
					</div>
				</div>

				<div class="row" style="margin:auto">
					<div class="col-sm-12 col-md-12" style="text-align:center">
						<span style="font-size:16px"><?= $c_tipo_descrip ?><br>
							<?= $recibo ?>
						</span>
					</div>
				</div>

				<div class="row" style="display:flex;margin-top:15px;">
					<div class="c-cliente" style="border-style: none; border-color:red;margin:auto;">
						<?= $etiqueta_razon ?>
					</div>
					<div class="c-datos" style="border-style: none; border-color:red;margin:auto;">
						:<?= $razon ?>
					</div>
				</div>

				<div class="row" style="display:flex">
					<div class="c-cliente" style="border-style: none; border-color:red;margin:auto;">
						<?= $etiqueta_ruc ?>
					</div>
					<div class="c-datos" style="border-style: none; border-color:red;margin:auto;">
						:<?= $doc_personal ?>
					</div>
				</div>

				<div class="row" style="display:flex">
					<div class="c-cliente" style="border-style: none; border-color:red;margin:auto;">
						Fecha
					</div>
					<div class="c-datos" style="border-style: none; border-color:red;margin:auto;">
						:<?= $fecha ?>
					</div>
				</div>				
								

				<div class="row" style="margin:auto;margin-top:15px;">
					<div class="col-sm-12 col-md-12">
						<center><table id="tabloide" class="table table-striped">
							<thead>
								<tr style="color:rgb(160,0,0)">
									<th style="min-width:70px">Producto</th>
									<th style="text-align: right">Cant.</th>
									<th style="text-align: right">P.U.</th>
									<!--<th style="text-align: right">Dscto.</th>-->
									<th style="text-align: right">SubTot</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$n = 0;
									$all_rows = $query->result();
									$rendered_groups = array();

									foreach($all_rows as $r){
										if(!empty($r->group_id)){
											// Item agrupado: solo renderizar una vez por grupo
											if(!isset($rendered_groups[$r->group_id])){
												$group_total = 0;
												foreach($all_rows as $r2){
													if($r2->group_id == $r->group_id){
														$group_total += $r2->subtotal;
													}
												}
												$rendered_groups[$r->group_id] = true;
												$n++;
												echo "<tr>";
												echo $this->fm->celda($r->group_name);
												echo $this->fm->celda("1","2");
												echo $this->fm->celda(number_format($group_total,2),"2");
												echo $this->fm->celda(number_format($group_total,2),"2");
												echo "</tr>";
											}
										}else{
											// Item individual (logica original)
											$n++;
											echo "<tr>";

											if(is_null($r->comment)){
												$nombre_producto = $r->product_name;
											}else{
												if (strlen(trim($r->comment)) > 0){
													$nombre_producto = trim($r->comment);
												}else{
													$nombre_producto = $r->product_name;
												}
											}

											echo $this->fm->celda($nombre_producto);
											echo $this->fm->celda(number_format($r->quantity,2),"2");
											echo $this->fm->celda(number_format($r->net_unit_price,2),"2");
											echo $this->fm->celda(number_format($r->subtotal,2),"2");
											echo "</tr>";
										}
									}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th colspan="2" style="text-align:right;color:rgb(160,0,0)">SubTotal :</th>
									<th style="text-align: right"><?= $total1 ?></th>
								</tr>
									<?php if($total_discount > 0){ ?>
								<tr>
										<th></th>
										<th colspan="2" style="text-align:right;color:rgb(160,0,0)">Dscto Total :</th>
										<th style="text-align: right"><?= $total_discount ?></th>
								</tr>
									<?php } ?>
								<tr>
									<th></th>
									<th colspan="2" style="text-align:right;color:rgb(160,0,0)">Igv :</th>
									<th style="text-align: right"><?= $total_tax ?></th>
								</tr>
								<tr>
									<th></th>
									<th colspan="2" style="text-align:right;color:rgb(160,0,0)">Total :</th>
									<th style="text-align: right"><?= $grand_total ?></th>
								</tr>
							</tfoot>
						</table></center>
					</div>
				</div>

			   	<div class="row" style="display:flex;margin-top:15px;">
			   		<div class="col-sm-10" style="margin:auto">
			        
				      	<center>
				      		<div id="qrcode" style="border-style: none; border-color:red;">
				      		</div>
				     	</center>

			        </div>
			    </div>

			   	<div class="row" style="display:flex;margin-top:15px;">
			   		<div class="col-sm-10" style="margin:auto;font-size: 11px;">
						<!--No se aceptan Devoluciones. Cambio de mercaderia max. 48 horas previa presentacion de su comprobante.<br>
						GRACIAS POR SU COMPRA-->
						<p><?= $nota_pie ?></p>
						<p>Esta es una representaci&oacute;n impresa <?=$cDesComprobante?>, generada en el Sistema de SUNAT. Puede verificarla utilizando su clave SOL.</p>
			        </div>
			    </div>
			</div><!-- EXPETO -->
			
		</div>

		<div class="row" style="display:flex;margin-top:15px;">
			<div class="col-sm-6 col-md-5 text-center">
				<a href="<?= base_url("sales/add") ?>" class="btn btn-primary">Ir a Ventas</a>&nbsp;&nbsp;&nbsp;
				<!--<button class="btn btn-primary" onclick="window.print()">Imprimir</button>-->
				<a href="javascript:imprSelec('seleccion')" class="btn btn-primary">Imprimir</a>
			</div>
		</div>


	</div>

	<script type="text/javascript" src="<?= base_url("assets/plugins/qrcodejs/qrcode.js") ?>"></script>
	<script type="text/javascript">
      var qrcode = new QRCode(document.getElementById("qrcode"),{
          text: "<?= $data_qr ?>",
          width: 96,
          height: 96,
          colorDark : "#000000",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });


		document.onkeydown = function(evt) { 
			evt = evt || window.event; 
			if (evt.keyCode == 27) { 
				//alert("Escape"); 
				window.location.href = "<?= base_url("sales/add") ?>"
			} 
		};

	function imprSelec(nombre) {
		var ficha = document.getElementById(nombre);
		var ventimp = window.open('', 'popimpr', 'width=800,height=600');

		ventimp.document.write(`
		    <html>
		      <head>
		        <title>Imprimir</title>
		      </head>
		      <body>
		        ${ficha.innerHTML}
		      </body>
		    </html>`);

	  	ventimp.document.close();

	  	// Esperar a que carguen las imágenes antes de imprimir
	  	ventimp.onload = function() {
	    	ventimp.focus();
	    	ventimp.print();
	    	ventimp.close();
	  	};
	}	
</script>

</body>
</html>