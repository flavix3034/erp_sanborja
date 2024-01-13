<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
	$empresa_id = $_SESSION["empresa_id"];
	$store_id 	= $_SESSION["store_id"];
	//$year 		= 2023;
	//$mes 		= 3;
	
	// Obteniendo Ganancia
	$cSql = "select sum(grand_total) totales from tec_sales where store_id = ? and extract(year from date(date)) = ? and extract(month from date(date)) = ?";
	//die($cSql);
	$query = $this->db->query($cSql,array($store_id, $anno, $mes));
	$ganancia = 0;
	$factor_ganancia = 0.52;
	foreach($query->result() as $r){
		$ganancia = floatval($r->totales)*$factor_ganancia;
	}

	$cSql = "select * from gastos_fijos where empresa_id = $empresa_id";
	$result 	= $this->db->query($cSql)->result_array(); // ,array($empresa_id)
	$alquiler 	= item_gasto_fijo($result,'ALQUILER');
	$internet 	= item_gasto_fijo($result,'INTERNET');
	$agua 		= item_gasto_fijo($result,'AGUA');
	$luz 		= item_gasto_fijo($result,'LUZ');
	$arbitrios 	= item_gasto_fijo($result,'ARBITRIOS');
	$sunat 		= item_gasto_fijo($result,'SUNAT');
	$sueldos 	= item_gasto_fijo($result,'SUELDOS');
	$menus 		= item_gasto_fijo($result,'MENUS');
	$total_gastos 	= $alquiler + $internet + $agua + $luz + $arbitrios + $sunat + $sueldos + $menus;
	$total_general 	= $ganancia - $total_gastos;
	
	function item_gasto_fijo($result, $item){
		foreach($result as $r) {
			if($r["gasto"] == $item){ 
				return floatval($r["monto"]);
			}
		}
		return 0;
	}
?>
<div class="row">
	<div class="col-12">
		<span style="font-size:22px;">Ganancia Mensual</span> (<?= $factor_ganancia * 100 ?>% de la Venta Total Bruta)<br><br>
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		.
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="ganancia" id="ganancia" class="form-control" value="<?=$ganancia?>">
	</div>
</div>

<div class="row">
	<div class="col-12">
		<h3>Gastos Mensuales</h3>
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		ALQUILER
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="alquiler" id="alquiler" class="form-control" value="<?=$alquiler?>">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		INTERNET
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="internet" id="internet" class="form-control" value="<?=$internet?>">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		AGUA
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="agua" id="agua" class="form-control" value="<?=$agua?>">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		LUZ
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="luz" id="luz" class="form-control" value="<?=$luz?>">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		ARBITRIOS
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="arbitrios" id="arbitrios" class="form-control" value="<?=$arbitrios?>">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		SUNAT
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="sunat" id="sunat" class="form-control" value="<?=$sunat?>">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		SUELDOS
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="sueldos" id="sueldos" class="form-control" value="<?=$sueldos?>">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		MENUS
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<input type="text" name="menus" id="menus" class="form-control" value="<?=$menus?>">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		<br>
		<b>TOTAL GASTOS</b>
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<br>
		<input type="text" name="total_gastos" id="total_gastos" class="form-control" value="<?=$total_gastos?>" style="font-style:bold!important">
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		<button type="button" onclick="recalcular()">Recalcular</button>
	</div>
</div>

<div class="row">
	<div class="col-6 col-sm-3 col-md-3 col-lg-2">
		<br>
		<b>GANANCIA REAL:</b>
	</div>
	<div class="col-6 col-sm-3 col-md-2 col-lg-1 text-right">
		<br>
		<input type="text" name="total_general" id="total_general" class="form-control" value="<?=$total_general?>" style="font-style:bold!important">
	</div>
</div>

<div class="row">
	<div class="col-12 col-sm-6 col-md-6 col-lg-4">
		Nota.- En los sueldos se está incluyendo 400 soles de comisión. En Sunat está incluido 100 del Contador.
	</div>
</div>

<script type="text/javascript">
	function recalcular(){
		let ganancia 	= document.getElementById('ganancia').value*1;
		let alquiler 	= document.getElementById('alquiler').value*1;
		let internet 	= document.getElementById('internet').value*1;
		let agua 		= document.getElementById('agua').value*1;
		let luz 		= document.getElementById('luz').value*1;
		let arbitrios 	= document.getElementById('arbitrios').value*1;
		let sunat 		= document.getElementById('sunat').value*1;
		let sueldos 	= document.getElementById('sueldos').value*1;
		let menus 		= document.getElementById('menus').value*1;
		let total_gastos 	= alquiler + internet + agua + luz + arbitrios + sunat + sueldos + menus;
		let total_general 	= ganancia - total_gastos;

		document.getElementById('total_gastos').value = total_gastos;
		document.getElementById('total_general').value = total_general;
	}
</script>
