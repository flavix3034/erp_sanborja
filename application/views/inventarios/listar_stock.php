<div class="row">
	<div class="col-12 col-sm-11 col-md-8 col-lg-7 col-xl-6" style="margin-top:15px;">
		<?php
			/*
			$estilos = 'padding: 0px 10px 0px 10px; border-style:solid; border-width:1px; border-color:rgb(100,160,230); height:30px;';
			$est_cab = 'padding: 0px 10px 0px 10px; border-style:solid; border-width:1px; border-color:rgb(100,160,230); height:30px; font-weight:bold; background-color:orange;';

			echo "Tienda : " . $store_id . "<br>";

			if(strlen($store_id)>0){
				echo "<table class='' style=\"\">";
				echo "<tr>";
				echo $this->fm->celda_h("ID",0,$est_cab);
				echo $this->fm->celda_h("Producto",0,$est_cab);
				echo $this->fm->celda_h("Marca",0,$est_cab);
				echo $this->fm->celda_h("Stock",0,$est_cab);
				echo "</tr>";
				
				if (isset($q_lista_stock)){
					foreach($q_lista_stock->result() as $r){
						echo "<tr>";
						echo $this->fm->celda($r->product_id,0,$estilos);
						echo $this->fm->celda($r->name,0,$estilos);
						echo $this->fm->celda($r->marca,0,$estilos);
						echo $this->fm->celda($r->stock,2,$estilos);
						echo "</tr>";
					}
				}
				echo "</table>";
			}
			*/
		?>

		<!--<h2 style="margin-bottom:26px">Categorias</h2>-->
		<table id="example" class="display" style="width:100%; font-size: 12px; margin-bottom: 20px;">
			<thead>
				<tr>
					<!-- "product_id", "name", "marca", "stock" -->
					<th style="max-width: 35px;">Id Producto</th>
					<th>Nombre</th>
					<th>Marca</th>
					<th>Stock</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>

	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
        	dom: 'Bfrtip',
            buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print'],
            "ajax": "<?= base_url("inventarios/get_listar_stock") ?>"
        });
    });
</script>