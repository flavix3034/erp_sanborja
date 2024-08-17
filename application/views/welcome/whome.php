<div class="row" style="padding-top:15px">

	<div class="col-xs-12 col-sm-7 col-lg-5">
		
		<h4>Ventas de las &uacute;ltimas 2 semanas (<?= $_SESSION["nombre_tienda"] ?>)</h4>

		<?= $mitabla ?>

	</div>

	<div class="col-xs-12 col-sm-5 col-lg-5">

		<h4>Ventas Semanales:</h4>

		<?= $tabla_semanal ?>

		<h4>Ventas x Categoria (Ultimos 30 dias):</h4>

		<?= $categorias ?>

	</div>

</div>