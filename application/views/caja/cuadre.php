<section>

	<div class="row">
		<div class="col-12 col-sm-12 col-md-10 col-lg-8">
			<h2>Caja</h2>
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-sm-12 col-md-10 col-lg-8">
		
		<?php
			// a.id, a.caja_id, a.fecha, a.monto_ini, a.monto_fin, a.estado_cierre
			$result 		= $query1->result_array();
			$monto_ini = 0;
			foreach($result as $r){ $monto_ini = floatval($r["monto_ini"]); }
			$cols 			= array("caja_id","fecha","monto_ini","monto_fin","estado_cierre");
			$cols_titulos 	= array("Caja","Fecha","Monto_ini","Monto_fin","Cierre");
			$ar_align 		= array("0","0","0","0","0");
			$ar_pie 		= $ar_align;

			echo $this->fm->crea_tabla_result($result, $cols, $cols_titulos, $ar_align, $ar_pie);
		?>	

		</div>
	</div>

	<div class="row">
		<div class="col-12 col-sm-12 col-md-10 col-lg-8">
			<h2>Ventas</h2>
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-sm-12 col-md-10 col-lg-8">
		
		<?php
			$result 		= $query2->result_array();
			$nTotal 		= 0;
			foreach($result as $r){
				$nTotal += floatval($r["grand_total"]);
			}
			echo "<h1>".$nTotal."</h1>";
		?>	

		</div>
	</div>

	<div class="row">
		<div class="col-12 col-sm-12 col-md-10 col-lg-8">
			<h2>Compras/Gastos</h2>
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-sm-12 col-md-10 col-lg-8">
		
		<?php
			$result 		= $query3->result_array();
			$nTotal_c 		= 0;
			foreach($result as $r){
				$nTotal_c 	+= floatval($r["total"]); 
			}
			echo "<h1>".$nTotal_c."</h1>";
		?>	

		</div>
	</div>

	<div class="row">
		<div class="col-12 col-sm-12 col-md-10 col-lg-8">
			<h2>Monto Final</h2>
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-sm-12 col-md-10 col-lg-8">
		
		<?php
			$result 		= $query3->result_array();
			$nTotal_c 		= 0;
			foreach($result as $r){
				$nTotal_c 	+= floatval($r["total"]); 
			}
			echo "<h1>" . ($monto_ini + $nTotal - $nTotal_c) . "</h1>";
		?>	

		</div>
	</div>

</section>