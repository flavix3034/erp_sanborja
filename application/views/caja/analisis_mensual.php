<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
?>
<?php echo form_open_multipart(base_url("caja/analisis_mensual"), 'class="validation" id="form_compra" onsubmit="return guardar_compra()"'); ?>
	<div class="row" style="margin-top:20px">
		<div class="col-6 col-sm-4 col-md-3 col-lg-2">
			<label>A&ntilde;o</label>
			<input type="number" name="anno">
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2">
			<label>Mes</label>
			<input type="number" name="mes">
		</div>
		<div class="col-6 col-sm-4 col-md-3 col-lg-2">
			<button type="submit" class="btn btn-primary" style="background-color:rgb(40,100,170)!important">Enviar</button>
		</div>
	</div>
<?php echo form_close(); ?>