		<?php
			//a.tipoDoc, concat(a.serie,\'-\',a.correlativo) recibo, a.customer_name, concat(d.cf1,\' \',d.cf2) doc_personal, a.date(date) as fech
			foreach($query->result() as $r){
				$tipo 		= $r->tipo_documento;
				$recibo 		= $r->recibo;
				
				if($tipo == 'Boleta'){ // Boleta : 2
	            //$tipoDoc_       = "03";
   	         $tipoDoc_client = "1"; // DNI
	         }elseif($tipo == 'Factura'){ // Factura : 1
	            //$tipoDoc_       = "01";
	            $tipoDoc_client = "6"; // RUC
	         }else{
	         	$tipoDoc_client = "1";
	         }
				$razon 			= $r->razon;
				$doc_personal 	= $r->doc_personal;
				$fecha 			= $r->fecha;
				$total1 			= number_format($r->total,2);
				$total_discount = number_format((is_null($r->total_discount) ? 0 : $r->total_discount),2);
				$total_tax 		= number_format($r->total_tax,2);
				$grand_total 	= number_format($r->grand_total,2);

			}

			$cSql = "select id, name, code, address1, city as provincia, state as distrito, nombre_empresa, ruc".
				" from tec_stores where id = ?";
			
				//die("store_id:" . $_SESSION["store_id"]);

			$query2 = $this->db->query($cSql,array($_SESSION["store_id"]));
			foreach($query2->result() as $r){
				$nombre_comercial 	= $r->name;
				$nombre_empresa 	= $r->nombre_empresa;
				$direccion 			= $r->address1;
				$ruc 				= $r->ruc;
			}

			$ar_datos1 = explode("-",$recibo);
			$serie 	= $ar_datos1[0];
			$numero 	= $ar_datos1[1];

			$RUC 				= $ruc;
			$TIPO 			= $tipo;
			$SERIE 			= $serie;
			$NUMERO 			= $numero;
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
		<div class="row" style="border-style: none; border-width: 2px; border-color:rgb(60,60,60); padding:5px;">
			
			<div class="col-sm-12 col-md-12" style="border-style: solid; border-width: 2px; border-color:rgb(60,60,60); padding:3px;"><!-- EXPETO -->
				<div class="row" style="">
					<div class="col-sm-12 col-lg-12" style="text-align:center">
						<img src="<?= base_url("imagenes/logo1.png") ?>" height="40px">
					</div>
				</div>

				<div class="row" style="margin-bottom:10px; margin:auto">
					<div class="col-sm-12 col-md-12" style="text-align:center">
						<span style="font-size:18px"><?= $nombre_comercial ?></span><br>
						<span style="font-size:16px"><?= $nombre_empresa ?></span><br>
						<span style="font-size:16px"><?= $direccion ?></span><br>
						<span style="font-size:16px">RUC: <?= $ruc ?></span><br>
					</div>
				</div>

				<div class="row" style="margin:auto">
					<div class="col-sm-12 col-md-12" style="text-align:center">
						<span style="font-size:18px"><?= $tipo ?>&nbsp;&nbsp;<?= $recibo ?></span>
					</div>
				</div>

				<div class="row" style="margin:auto">
					<div class="col-sm-12 col-md-12" style="border-style: none; border-color:red">
						<center><table>
							<td style="font-weight:bold; font-size: 14px; color:rgb(160,0,0);">
								<br>
								<?= $etiqueta_razon ?><br>
								<?= $etiqueta_ruc ?><br>
								Fecha<br><br>
							</td>
							<td style="font-weight:bold; font-size: 14px;">
								<br>
								:<?= $razon ?><br>
								:<?= $doc_personal ?><br>
								:<?= $fecha ?><br><br>
							</td>
						</table></center>
					</div>
				</div>
				<div class="row" style="margin:auto">
					<div class="col-sm-12 col-md-12">
						<table id="tabloide" class="table table-striped">
							<thead>
								<tr style="color:rgb(160,0,0)">
									<th>Producto</th>
									<th>Cantidad</th>
									<th>P.U.</th>
									<!--<th>Dscto.</th>-->
									<th>Subtotal</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$n = 0;
									foreach($query->result() as $r){
										$n++;
										echo "<tr>";
										//echo $this->fm->celda($r->name . ' ' . $r->marca . ' ' . $r->modelo);
										echo $this->fm->celda($r->product_name);
										echo $this->fm->celda(number_format($r->quantity,2));
										echo $this->fm->celda(number_format($r->net_unit_price,2));
										//echo $this->fm->celda(number_format($r->discount,2));
										echo $this->fm->celda(number_format($r->subtotal,2));
										echo "</tr>";
									}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<!--<th></th>-->
									<th colspan="2" style="text-align:right;color:rgb(160,0,0)">SubTotal :</th>
									<th><?= $total1 ?></th>
								</tr>
									<?php if($total_discount > 0){ ?>
								<tr>
										<th></th>
										<!--<th></th>-->
										<th colspan="2" style="text-align:right;color:rgb(160,0,0)">Dscto Total :</th>
										<th><?= $total_discount ?></th>
								</tr>
									<?php } ?>
								<tr>
									<th></th>
									<!--<th></th>-->
									<th colspan="2" style="text-align:right;color:rgb(160,0,0)">Igv :</th>
									<th><?= $total_tax ?></th>
								</tr>
								<tr>
									<th></th>
									<!--<th></th>-->
									<th colspan="2" style="text-align:right;color:rgb(160,0,0)">Total :</th>
									<th><?= $grand_total ?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>

			   <div class="row" style="margin:auto;">
			        <script type="text/javascript" src="<?= base_url("assets/plugins/qrcodejs/qrcode.js") ?>"></script>

			        <div class="col-xs-4 col-sm-4 col-md-4">
			        </div>

			        <div id="qrcode" class="col-xs-6 col-sm-7 col-md-2" style="border-style: none; border-color:red">
			        </div>

			        <div class="col-xs-2 col-sm-1 col-md-6" style="padding-left:20px">
			        </div>

			        <script type="text/javascript">
			            var qrcode = new QRCode(document.getElementById("qrcode"),{
			                text: "<?= $data_qr ?>",
			                width: 128,
			                height: 128,
			                colorDark : "#000000",
			                colorLight : "#ffffff",
			                correctLevel : QRCode.CorrectLevel.H
			            });
			        </script>
			    </div>
			</div><!-- EXPETO -->
			
		</div>
		<!--<div class="row">
			<div class="col-sm-12 col-md-12 text-center">
				<a href="<?= base_url("sales/add") ?>" class="btn btn-primary">Ir a Ventas</a>&nbsp;&nbsp;&nbsp;
				<a href="javascript:imprSelec('seleccion')" class="btn btn-primary">Imprimir</a>
			</div>
		</div>-->
