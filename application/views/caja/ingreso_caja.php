<?php
	$monto = (isset($monto) ? $monto : "");
?>
<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div class="alert alert-<?= $alerta ?>" style="margin:10px"><?= $mensaje ?></div>
		</div>
	</div>
	<div class="row">
		<h1><?= $title ?></h1>	
	</div>
	<form name="form1" id="form1" method="post" action="<?= base_url("caja/grabar") ?>">
		<div class="row" style="margin-bottom:15px">
			<div class="col-sm-2">
				<label>Multirecibo:</label>
				<select name="multirecibo" id="multirecibo" class="form-control">
					<option value="1">Individual</option>
					<option value="2">M&uacute;ltiple</option>
				</select>
			</div>		
			<div class="col-sm-2">
				<label>Recibo:</label>
				<input type="text" name="recibo" id="recibo" class="form-control">
			</div>		
			<div class="col-sm-2">
				<label>Fecha:</label>
				<input type="date" name="fecha" id="fecha" class="form-control">
			</div>		
		</div>
		<div class="row" style="margin-bottom:15px">
			<div class="col-sm-4">
				<label>Afiliado:</label>
				<?php
					$nOrder = 1; // cod_afi
					$result = $this->welcome_model->afiliados($nOrder);  
					$ar = array();
					$ar[] = "";
					$i = 0;
					foreach($result as $r){
						$i++;
						$ar[trim($r->cod_afi)] = (strlen($r->nombre)>0 ? str_pad(trim($r->cod_afi),10,"_") . " " . str_pad(trim($r->nombre),40,"_") . " " . 
							str_pad(trim($r->tipo1),10,"_") . " " . $r->dato1: 'sin-nombre');
					}
					echo form_dropdown('cod_afi',$ar,'','class="form-control tip" id="cod_afi" required="required" style="font-family:courier"');
				?>
			</div>		

			<div class="col-sm-4">
				<label>Concepto:</label>
				<?php
					$result = $this->welcome_model->conceptos();  
					$ar = array();
					$i = 0;
					foreach($result as $r){
						$i++;
						$cad = str_pad(trim($r->concepto),30,"_") . " " . str_pad(trim($r->moneda),1,"_");
						$ar[trim($r->tipo)] = (strlen($r->concepto)>0 ? $cad : "");
					}
					echo form_dropdown('concepto',$ar,'','class="form-control tip" id="tipoDoc" required="required" style="font-family:courier"');
				?>
			</div>		
			<div class="col-sm-1">
				<label>Cantidad:</label>
				<input type="text" name="cantidad" id="cantidad" class="form-control">
			</div>		
		</div>
		<div class="row" style="margin-bottom:15px">
			<div class="col-sm-2">
				<label>Mes:</label>
				<?php
					$ar = array();
					$ar["1"] = "ENERO";
					$ar["2"] = "FEBRERO";
					$ar["3"] = "MARZO";
					$ar["4"] = "ABRIL";
					$ar["5"] = "MAYO";
					$ar["6"] = "JUNIO";
					$ar["7"] = "JULIO";
					$ar["8"] = "AGOSTO";
					$ar["9"] = "SETIEMBRE";
					$ar["10"] = "OCTUBRE";
					$ar["11"] = "NOVIEMBRE";
					$ar["12"] = "DICIEMBRE";
					echo form_dropdown('mes',$ar,'','class="form-control tip" id="concepto" required="required" style="font-family:courier"');
				?>			
			</div>		
			<div class="col-sm-2">
				<label>Año:</label>
				<?php
					$ar = array();
					$ar["2017"] = "2017";
					$ar["2018"] = "2018";
					$ar["2019"] = "2019";
					$ar["2020"] = "2020";
					$ar["2021"] = "2021";
					$ar["2022"] = "2022";
					$ar["2023"] = "2023";
					$ar["2024"] = "2024";
					$ar["2025"] = "2025";
					echo form_dropdown('anno',$ar,'','class="form-control tip" id="anno" required="required" style="font-family:courier"');
				?>			
			</div>		
			<div class="col-sm-2">
				<label>Monto:</label>
				<?= form_input('monto', $monto, 'class="form-control tip" id="monto"'); ?>
			</div>		
			<div class="col-sm-2">
				<label>Moneda:</label>
				<?php
					$ar = array('S'=>'Soles','D'=>'Dolares');
					echo form_dropdown('cod_mon',$ar,'','class="form-control tip" id="cod_mon" required="required" style="font-family:courier"'); 
				?>
			</div>		
		</div>

		<div class="row" style="margin-bottom:15px">
			<div class="col-xs-12 col-sm-6">
				<label>Observacion:</label><br>
				<textarea rows="2" cols="60" id="obs" name="obs"></textarea>
			</div>
			<div class="col-xs-12 col-sm-2">
				<br>
				<button type="button" onclick="validar()" class="form-control btn btn-success">Aceptar</button>
			</div>
		</div>

		<div class="row" style="margin-bottom:15px">
			<div class="col-xs-12 col-sm-6" id="tabla-items">
				
			</div>
		</div>

	</form>
</div>
<script type="text/javascript">
	function validar(){
		monto = document.getElementById("monto").value
		if(monto.length == 0){
			alert("Debe ingresar el monto")
			return false
		}

		cod_afi = document.getElementById("cod_afi").value
		if(cod_afi == "0" || cod_afi == ""){
			alert("Ingrese el afiliado")
			return false
		}

		document.form1.submit()
	}
</script>