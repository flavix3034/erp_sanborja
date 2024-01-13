<?php echo form_open_multipart("inventarios/actualizar_stock", 'class="validation" id="form_compra"'); ?>

<div class="row">
	<div class="col-sm-12">
		<?php
			if(isset($datis)){
				echo $datis;
			}
		?>
	</div>
</div>

<div class="row">

	<div class="col-sm-3" style="margin-top:15px">
		<button type="submit" class="btn btn-danger">Actualizar Tabla temporal de Stock</button>
		<input type="hidden" name="modo" value="ejecutar">
	</div>

	<div class="col-sm-9" style="margin-top:15px">
		Nota.- Actualiza la tabla temporal de Stock con informacion del Kardex.
	</div>
	
</div>

<?php echo form_close(); ?>