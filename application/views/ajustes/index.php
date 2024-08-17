<?= form_open_multipart("ajustes/save", 'class="validation" id="form1"'); ?>
<div class="row" style="margin-top:15px">
	<div class="col-sm-4">
		<label>Pie del Comprobante:</label><br>
		<textarea name="nota_pie" id="nota_pie" class="form-control" rows="4"><?= $nota_pie ?></textarea>
	</div>
</div>
<div class="row" style="margin-top:15px">
	<div class="col-sm-4">
		<button type="submit" class="btn btn-primary">Actualiza</button>
	</div>
</div>
<?= form_close() ?>