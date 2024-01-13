<div class="row">
	<div class="col-sm-12" style="margin-top:15px;">
		<?php
			$estilos = 'padding: 0px 10px 0px 10px; border-style:solid; border-width:1px; border-color:rgb(100,160,230); height:30px;';
			$est_cab = 'padding: 0px 10px 0px 10px; border-style:solid; border-width:1px; border-color:rgb(100,160,230); height:30px; font-weight:bold; background-color:orange;';

			echo "Tienda : " . $store_id . "<br>";

			if(strlen($store_id)>0){
				echo "<table class='' style=\"\">";
				echo "<tr>";
				echo $this->fm->celda_h("ID",0,$est_cab);
				echo $this->fm->celda_h("Producto",0,$est_cab);
				echo $this->fm->celda_h("Marca",0,$est_cab);
				echo $this->fm->celda_h("Modelo",0,$est_cab);
				echo $this->fm->celda_h("Stock",0,$est_cab);
				echo "</tr>";
				
				if (isset($q_lista_stock)){
					foreach($q_lista_stock->result() as $r){
						echo "<tr>";
						echo $this->fm->celda($r->product_id,0,$estilos);
						echo $this->fm->celda($r->name,0,$estilos);
						echo $this->fm->celda($r->marca,0,$estilos);
						echo $this->fm->celda($r->modelo,0,$estilos);
						echo $this->fm->celda($r->stock,2,$estilos);
						echo "</tr>";
					}
				}
				echo "</table>";
			}
		?>
	</div>
</div>