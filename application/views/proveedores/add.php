<?php
	$id        = "";
	$nombre    = set_value("nombre");
	$ruc       = set_value("ruc");
	$correo    = set_value("correo");
	$phone     = set_value("phone");
	$phone2    = set_value("phone2");
	$contacto  = set_value("contacto");
	$direccion = set_value("direccion");
	$notas     = set_value("notas");
	if(isset($query_p1)){
		foreach($query_p1->result() as $r){
			$id        = $r->id;
			$nombre    = $r->nombre;
			$ruc       = $r->ruc;
			$correo    = $r->correo;
			$phone     = $r->phone;
			$phone2    = isset($r->phone2) ? $r->phone2 : '';
			$contacto  = isset($r->contacto) ? $r->contacto : '';
			$direccion = $r->direccion;
			$notas     = isset($r->notas) ? $r->notas : '';
		}
	}
?>

<style>
	.prov-card {
		background: #fff;
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		padding: 24px 28px;
		max-width: 860px;
		margin-top: 10px;
	}
	.prov-card .form-group label {
		font-weight: 600;
		font-size: 12px;
		color: #555;
		margin-bottom: 4px;
	}
	.prov-card .form-control {
		border-radius: 5px;
		font-size: 13px;
	}
	.prov-section-title {
		font-size: 12px;
		font-weight: 700;
		text-transform: uppercase;
		color: #888;
		letter-spacing: 0.5px;
		border-bottom: 1px solid #eee;
		padding-bottom: 6px;
		margin: 18px 0 14px;
	}
</style>

<?= form_open(base_url("proveedores/save"), 'method="post" name="form1" id="form1"'); ?>
<?= form_hidden('id', $id); ?>

<div class="prov-card">

	<!-- Fila 1: Razón Social + RUC -->
	<div class="row">
		<div class="col-sm-8 col-md-7">
			<div class="form-group">
				<label>Razón Social *</label>
				<input type="text" name="nombre" id="nombre" class="form-control" value="<?= htmlspecialchars($nombre) ?>" required placeholder="Nombre o razón social del proveedor">
			</div>
		</div>
		<div class="col-sm-4 col-md-3">
			<div class="form-group">
				<label>RUC</label>
				<input type="text" name="ruc" id="ruc" class="form-control" value="<?= htmlspecialchars($ruc) ?>" maxlength="11" placeholder="11 dígitos">
			</div>
		</div>
	</div>

	<!-- Fila 2: Correo + Teléfono + Teléfono 2 -->
	<div class="row">
		<div class="col-sm-4 col-md-4">
			<div class="form-group">
				<label>Correo electrónico</label>
				<input type="email" name="correo" id="correo" class="form-control" value="<?= htmlspecialchars($correo) ?>" placeholder="ejemplo@correo.com">
			</div>
		</div>
		<div class="col-sm-4 col-md-4">
			<div class="form-group">
				<label>Teléfono / Móvil</label>
				<input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" placeholder="Ej: 987654321">
			</div>
		</div>
		<div class="col-sm-4 col-md-4">
			<div class="form-group">
				<label>Teléfono adicional</label>
				<input type="text" name="phone2" id="phone2" class="form-control" value="<?= htmlspecialchars($phone2) ?>" placeholder="Línea adicional">
			</div>
		</div>
	</div>

	<!-- Fila 3: Contacto directo + Dirección -->
	<div class="row">
		<div class="col-sm-4 col-md-4">
			<div class="form-group">
				<label>Contacto directo</label>
				<input type="text" name="contacto" id="contacto" class="form-control" value="<?= htmlspecialchars($contacto) ?>" placeholder="Nombre del contacto (opcional)">
			</div>
		</div>
		<div class="col-sm-8 col-md-8">
			<div class="form-group">
				<label>Dirección</label>
				<input type="text" name="direccion" id="direccion" class="form-control" value="<?= htmlspecialchars($direccion) ?>" placeholder="Av. / Jr. / Calle...">
			</div>
		</div>
	</div>

	<!-- Fila 5: Notas / Especialidad (ancho completo) -->
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				<label>Notas / Especialidad</label>
				<textarea name="notas" id="notas" class="form-control" rows="3" placeholder="Ej: Vende pantallas originales Samsung, baterías, herramientas de microelectrónica..."><?= htmlspecialchars($notas) ?></textarea>
			</div>
		</div>
	</div>

	<!-- Botones -->
	<div class="row" style="margin-top: 10px;">
		<div class="col-sm-12">
			<button type="button" class="btn btn-primary" onclick="validar()">
				<i class="glyphicon glyphicon-floppy-disk"></i> Guardar
			</button>
			&nbsp;
			<a href="<?= base_url('proveedores') ?>" class="btn btn-default">
				<i class="glyphicon glyphicon-remove"></i> Cancelar
			</a>
		</div>
	</div>

</div>

<!-- Submit oculto -->
<button type="submit" name="submit1" id="submit1" style="display:none;"></button>

<?= form_close() ?>

<script type="text/javascript">

	// Solo números en RUC
	document.getElementById('ruc').addEventListener('input', function() {
		this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
	});

	function validar() {
		var ruc = document.getElementById('ruc').value;
		if (ruc.length > 0 && ruc.length !== 11) {
			alert('El RUC debe tener exactamente 11 dígitos.');
			document.getElementById('ruc').focus();
			return;
		}
		document.getElementById('submit1').click();
	}
</script>
