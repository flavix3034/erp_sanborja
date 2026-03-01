<?php
	$modo = isset($empleado) ? "U" : "I";
	$e = isset($empleado) ? $empleado : null;
?>

<?php if(isset($msg)): ?>
<div class="alert alert-<?= isset($rpta_msg) ? $rpta_msg : 'success' ?>"><?= $msg ?></div>
<?php endif; ?>

<div class="row filitas">
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4><i class="fa fa-<?= $modo == 'I' ? 'plus' : 'edit' ?>"></i> <?= $modo == 'I' ? 'Agregar' : 'Editar' ?> Empleado</h4>
			</div>
			<div class="panel-body">
				<form method="post" action="<?= base_url('empleados/save') ?>">
					<input type="hidden" name="modo" value="<?= $modo ?>">
					<?php if($modo == 'U'): ?>
					<input type="hidden" name="id" value="<?= $e->id ?>">
					<?php endif; ?>

					<div class="row filitas">
						<div class="col-sm-6">
							<label>Apellidos *</label>
							<input type="text" name="apellidos" class="form-control" value="<?= $e ? htmlspecialchars($e->apellidos) : '' ?>" required style="text-transform:uppercase;">
						</div>
						<div class="col-sm-6">
							<label>Nombres *</label>
							<input type="text" name="nombres" class="form-control" value="<?= $e ? htmlspecialchars($e->nombres) : '' ?>" required style="text-transform:uppercase;">
						</div>
					</div>

					<div class="row filitas">
						<div class="col-sm-3">
							<label>DNI</label>
							<input type="text" name="dni" class="form-control" value="<?= $e ? htmlspecialchars($e->dni) : '' ?>" maxlength="15">
						</div>
						<div class="col-sm-3">
							<label>Teléfono</label>
							<input type="text" name="telefono" class="form-control" value="<?= $e ? htmlspecialchars($e->telefono) : '' ?>" maxlength="20">
						</div>
						<div class="col-sm-3">
							<label>Cargo</label>
							<input type="text" name="cargo" class="form-control" value="<?= $e ? htmlspecialchars($e->cargo) : '' ?>" style="text-transform:uppercase;">
						</div>
						<div class="col-sm-3">
							<label>Area</label>
							<input type="text" name="area" class="form-control" value="<?= $e ? htmlspecialchars($e->area) : '' ?>" style="text-transform:uppercase;">
						</div>
					</div>

					<div class="row filitas">
						<div class="col-sm-3">
							<label>Fecha de Ingreso</label>
							<input type="date" name="fecha_ingreso" class="form-control" value="<?= $e ? $e->fecha_ingreso : '' ?>">
						</div>
					</div>

					<hr>
					<button type="submit" class="btn btn-success">
						<i class="fa fa-save"></i> <?= $modo == 'I' ? 'Registrar' : 'Actualizar' ?>
					</button>
					<a href="<?= base_url('empleados/index') ?>" class="btn btn-default">
						<i class="fa fa-arrow-left"></i> Volver
					</a>
				</form>
			</div>
		</div>
	</div>
</div>

<style>
.filitas { margin-bottom: 15px; }
.panel-heading h4 { margin: 0; font-size: 15px; font-weight: bold; }
</style>
